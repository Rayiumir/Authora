<?php

defined('ABSPATH') || exit;

function authora_public_scripts(){

    if( is_user_logged_in() ){
        return;
    }

    wp_enqueue_style(
        'authora-style',
        AUTHORA_LOGIN_CSS . 'authora.css',
        [],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION
    );

    wp_enqueue_script(
        'authora-script',
        AUTHORA_LOGIN_JS . 'authora.js',
        ['jquery'],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION,
        true
    );

    wp_localize_script( 'authora-script', 'authora', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('authora_login')
    ]);

}
add_action( 'wp_enqueue_scripts', 'authora_public_scripts' );