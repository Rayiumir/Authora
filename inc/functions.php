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

function sanitize_mobile( $mobile ){

    /**
     * Convert all chars to en digits
     */
    $western    = array('0','1','2','3','4','5','6','7','8','9');
    $persian    = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic     = ['٠',  '١',  '٢', '٣','٤', '٥', '٦','٧','٨','٩' ];
    $mobile      = str_replace( $persian, $western, $mobile );
    $mobile      = str_replace( $arabic, $western, $mobile );

    // .915 => 0915
    if( strpos( $mobile, '.' ) === 0 ){
        $mobile = '0' . substr( $mobile, 1 );
    }

    // 0098918 => 918
    if( strpos( $mobile, '0098' ) === 0 ){
        $mobile = substr( $mobile, 4 );
    }
    // 098910 => 910
    if( strlen( $mobile ) == 13 && strpos( $mobile, '098' ) === 0 ){
        $mobile = substr( $mobile, 3 );
    }
    // +98915 => 915
    if( strlen( $mobile ) == 13 && strpos( $mobile, '+98' ) === 0 ){
        $mobile = substr( $mobile, 3 );
    }
    // +98 915 => 915
    if( strlen( $mobile ) == 14 && strpos( $mobile, '+98 ' ) === 0 ){
        $mobile = substr( $mobile, 4 );
    }
    // 98915 => 915
    if( strlen( $mobile ) == 12 && strpos( $mobile, '98' ) === 0 ){
        $mobile = substr( $mobile, 2 );
    }
    // Prepend 0
    if( strpos( $mobile, '0' ) !== 0 ){
        $mobile = '0' . $mobile;
    }
    /**
     * check for all character was digit
     */
    if( ! ctype_digit( $mobile ) ){
        return '';
    }

    if( strlen( $mobile ) != 11 ){
        return '';
    }

    return $mobile;
}