<?php

defined('ABSPATH') || exit;

function authora_generate_code( $digits = 5 ){
    $code = '';
    for( $i = 0; $i < $digits; $i++ ){
        $code.= rand( ! $i ? 1 : 0, 9 );
    }
    return $code;
}

function authora_register_code( $mobile, $code, $expired_at ){

    global $wpdb;

    $data = [
        'mobile'         => $mobile,
        'code'          => $code,
        'expired_at'    => $expired_at,
        'created_at'    => current_time('mysql'),
        'updated_at'    => current_time('mysql'),
    ];

    $inserted = $wpdb->insert(
        $wpdb->authora_login,
        $data,
        '%s'
    );

    if( ! $inserted ){
        notificator_send_message( 'insert error for ' . $wpdb->authora_login . PHP_EOL . print_r( $data, true ) );
        new WP_Error( 'error_insertion', 'خطا در ثبت داده' );
    }

    return $wpdb->insert_id;

}