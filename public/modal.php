<?php

defined('ABSPATH') || exit;

function authora_login_modal(): void
{
    include( AUTHORA_LOGIN_VIEW . 'loginModal.php' );
}
add_action('wp_footer', 'authora_login_modal');
