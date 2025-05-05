<?php

defined('ABSPATH') || exit;

function authora_login() {
    // Handle login logic here
    // Validate mobile number and send OTP
    // Return response
}
add_action('wp_ajax_nopriv_authora_login', 'authora_login');