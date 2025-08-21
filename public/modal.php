<?php

defined('ABSPATH') || exit;

function authora_login_modal(): void
{
    if( ! is_user_logged_in(  ) ){
        include( AUTHORA_LOGIN_VIEW . 'loginModal.php' );
    }
}
add_action('wp_footer', 'authora_login_modal');

function authora_shortcode($atts) { 
    if (is_user_logged_in()) {
        return '';
    }

    $atts = shortcode_atts(array(
        'button_text' => __('ورود / عضویت', 'authora'),
        'button_class' => 'buttonLogin',
        'container_class' => '',
        'show_modal' => 'true'
    ), $atts);

    $output = '';
    
    if ($atts['container_class']) {
        $output .= '<div class="' . esc_attr($atts['container_class']) . '">';
    }

    if ($atts['show_modal'] === 'true') {
        $output .= '<a class="' . esc_attr($atts['button_class']) . '" href="#modal">' . esc_html($atts['button_text']) . '</a>';
    } else {
        $output .= '<div class="authora-login-form">';
        ob_start();
        include(AUTHORA_LOGIN_VIEW . 'loginFormContent.php');
        $output .= ob_get_clean();
        $output .= '</div>';
    }

    if ($atts['container_class']) {
        $output .= '</div>';
    }

    return $output;
}
add_shortcode('authora-login', 'authora_shortcode');
