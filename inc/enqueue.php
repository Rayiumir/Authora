<?php

defined('ABSPATH') || exit;

function authora_public_scripts(){

    // if( is_user_logged_in(  ) ){
    //     return;
    // }

    wp_enqueue_style(
        'authora-style',
        AUTHORA_LOGIN_CSS . 'modal.css',
        [],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION
    );

    wp_enqueue_script(
        'authora-script',
        AUTHORA_LOGIN_JS . 'modal.js',
        ['jquery'],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION,
        true
    );

}
add_action( 'wp_enqueue_scripts', 'authora_public_scripts' );