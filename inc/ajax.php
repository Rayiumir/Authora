<?php

defined('ABSPATH') || exit;

function authora_login() {
    global $wpdb;
    $result = [
        'message'   => __('An error occurred', 'authora')
    ];

    if(
        ! isset( $_REQUEST['mobile'] ) ||
        ! isset( $_REQUEST['_wpnonce'] ) ||
        ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'authora-login' )
    ){ 
        wp_send_json_error( $result, 401 );
    }

    $mobile = sanitize_mobile( $_REQUEST['mobile'] );

    if( ! $mobile ){
        $result['message']  = __('Invalid mobile number', 'authora');
        wp_send_json_error( $result, 401 );
    }

    $digit          = 5;
    $expire         = 200;
    $code           = authora_generate_code( $digit );

    $expired_at     = date( 'Y-m-d H:i:s', current_time('timestamp') + $expire );

    $inserted       = authora_register_code( $mobile, $code, $expired_at );
    
    if( is_wp_error( $inserted ) ){
        $result['message'] = $inserted->get_error_message();
        wp_send_json_error( $result, 503 );
    }

    $message = 'code: ' . $code;

    $sent_sms = authoraDrivers( $mobile, $code );
    
    if( is_wp_error( $sent_sms ) ){
        $result['message'] = $sent_sms->get_error_message();
        wp_send_json_error( $result, 400 );
    }

    $wpdb->update(
        $wpdb->authora_login,
        [
            'price' => $sent_sms->cost,
            'message_id' => $sent_sms->messageId,
        ],
        [
            'ID' => $inserted,
        ]
    );

    $result['message']  = sprintf(__('Enter the %d-digit code sent to %s', 'authora'), $digit, $mobile);
    $result['code']     = $code;
    $result['duration'] = $expire;
    $result['mobile']    = $mobile;
    $result['_wpnonce'] = wp_create_nonce( 'verify' . $mobile );

    wp_send_json_success( $result, 200 );

}
add_action('wp_ajax_nopriv_authora_login', 'authora_login');

function authora_verify(){
    
    $result = [
        'message'   => __('An error occurred', 'authora')
    ];

    if(
        ! isset( $_REQUEST['mobile'] ) ||
        ! isset( $_REQUEST['code'] ) ||
        ! isset( $_REQUEST['_wpnonce'] ) ||
        ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'verify'.  $_REQUEST['mobile'] )
    ){
        wp_send_json_error( $result, 401 );
    }

    $mobile  = sanitize_mobile( $_REQUEST['mobile'] );

    if( !$mobile ){
        $result['message']  = __('Invalid phone number', 'authora');
        wp_send_json_error( $result, 401 );
    }

    $code   = sanitize_text_field( $_REQUEST['code'] );

    global $wpdb;
    $verify = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->authora_login} WHERE mobile = %s ORDER BY created_at DESC",
            $mobile
        )
    );

    if( !$verify ){
        $result['message']  = __('Your verification request was not found', 'authora');
        wp_send_json_error( $result, 401 );
    }

    if( $verify->code != $code ){
        $result['message']  = __('Incorrect verification code, please try again', 'authora');
        wp_send_json_error( $result, 401 );
    }

    if( current_time('timestamp') >= strtotime( $verify->expired_at ) ){
        $result['message']  = __('Code has expired, please try again', 'authora');
        wp_send_json_error( $result, 401 );
    }
    
    $exists = getUserByMobile( $mobile );
    $user   = getOrMakeUser( $mobile );

    if( is_wp_error( $user ) ){
        $result['message']  = $user->get_error_message();
        wp_send_json_error( $result, 401 );
    }

    wp_clear_auth_cookie();
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID );

    // Login

    $data = [
        'user_id'   => $user->ID,
        'status'    => $exists ? 'login' : 'register',
        'updated_at'    => current_time('mysql'),
    ];

    $wpdb->update(
        $wpdb->authora_login,
        $data,[
            'ID' => $verify->ID
        ]
    );

    $result['message'] = __('Login successful', 'authora');
    wp_send_json_success( $result, 200 );
    
}
add_action( 'wp_ajax_nopriv_authora_verify', 'authora_verify' );