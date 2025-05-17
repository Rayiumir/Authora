<?php

defined('ABSPATH') || exit;

function authora_login() {
    global $wpdb;
    $result = [
        'message'   => 'خطایی رخ داده است'
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
        $result['message']  = 'موبایل صحیح نیست';
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

    $result['message']  = 'کد ' . $digit . ' رقمی ارسال شده به شماره ' . $mobile . ' را وارد کنید';
    $result['code']     = $code;
    $result['duration'] = $expire;
    $result['mobile']    = $mobile;
    $result['_wpnonce'] = wp_create_nonce( 'verify' . $mobile );

    wp_send_json_success( $result, 200 );

}
add_action('wp_ajax_nopriv_authora_login', 'authora_login');