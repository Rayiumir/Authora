<?php

defined('ABSPATH') || exit;

function authora_login_modal(): void
{
    if( ! is_user_logged_in(  ) ){
        include( AUTHORA_LOGIN_VIEW . 'loginModal.php' );
    }
}
add_action('wp_footer', 'authora_login_modal');
