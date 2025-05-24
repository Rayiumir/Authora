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

    if (is_rtl()) {
        wp_enqueue_style(
            'authora-rtl', 
            AUTHORA_LOGIN_CSS . 'modal-rtl.css', 
            [],
            defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION,
        );
    }

    wp_localize_script( 'authora-script', 'authora', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('authora_login'),

        'i18n' => array(
            'sending' => __('Sending...', 'authora'),
            'resend' => __('Resend', 'authora'),
            'error_occurred' => __('An error occurred. Please try again.', 'authora'),
            'connection_error' => __('Could not connect to server.', 'authora')
        )
    ]);

}
add_action( 'wp_enqueue_scripts', 'authora_public_scripts' );

function admin_scripts(){

    wp_enqueue_style(
        'setting-style',
        AUTHORA_LOGIN_CSS . 'setting.css',
        [],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION
    );

    wp_enqueue_script(
        'setting-script',
        AUTHORA_LOGIN_JS . 'setting.js',
        ['jquery'],
        defined('WP_DEBUG') && WP_DEBUG ? time() : AUTHORA_LOGIN_VERSION,
        true
    );

}
add_action( 'admin_head', 'admin_scripts' );