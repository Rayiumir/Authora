<?php

// Modify WordPress login form
function authora_modify_login_form() {
    if (get_option('authora_enable_mobile_login') !== '1') {
        return;
    }

    // The styles and scripts are now enqueued in inc/enqueue.php
    // HTML structure for the login form
    ?>
    <div class="login">
        <form name="loginform" id="loginform" action="login" method="post" data-mobile="">
            <p class="message">ورود با شماره موبایل</p>
            <div class="authora-form-container">
                <div class="authora-mobile-form">
                    <p>
                        <label for="authora_mobile">شماره موبایل</label><br>
                        <input type="text" name="authora_mobile" id="authora_mobile" class="input" value="" size="20" required>
                    </p>
                    <p class="submit">
                        <input type="button" name="authora_send_code" id="authora_send_code" class="button button-primary" value="ارسال کد تایید">
                    </p>
                </div>
            </div>
        </form>
    </div>
    <?php
}
add_action('login_form', 'authora_modify_login_form');

// Handle AJAX requests for sending verification code
function authora_handle_send_code() {
    if (!check_ajax_referer('authora_send_code', 'nonce', false)) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }

    $mobile = isset($_POST['mobile']) ? sanitize_text_field($_POST['mobile']) : '';
    if (empty($mobile)) {
        wp_send_json_error(['message' => 'شماره موبایل الزامی است']);
        return;
    }

    // Generate verification code (5 digits)
    $code = wp_rand(10000, 99999);
    
    // Start session if not started
    if (!session_id()) {
        session_start();
    }
    
    // Store code in session
    $_SESSION['authora_verification_code'] = (string)$code;
    $_SESSION['authora_verification_mobile'] = $mobile;
    $_SESSION['authora_verification_time'] = time();
    
    // Debug information
    error_log('Setting verification code:');
    error_log('Mobile: ' . $mobile);
    error_log('Code: ' . $code);
    error_log('Session ID: ' . session_id());
    error_log('Session Data: ' . print_r($_SESSION, true));
    
    // Send SMS using authoraDrivers
    try {
        $result = authoraDrivers($mobile, $code);
        if ($result) {
            wp_send_json_success([
                'message' => 'کد تایید ارسال شد'
            ]);
        } else {
            wp_send_json_error(['message' => 'خطا در ارسال پیامک']);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'خطا در ارسال پیامک: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_nopriv_authora_send_verification_code', 'authora_handle_send_code');
add_action('wp_ajax_authora_send_verification_code', 'authora_handle_send_code');

// Handle AJAX requests for verifying code
function authora_handle_verify_code() {
    if (!check_ajax_referer('authora_verify_code', 'nonce', false)) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }

    $mobile = isset($_POST['mobile']) ? sanitize_text_field($_POST['mobile']) : '';
    $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';

    if (empty($mobile) || empty($code)) {
        wp_send_json_error(['message' => 'اطلاعات ناقص است']);
        return;
    }

    // Start session if not started
    if (!session_id()) {
        session_start();
    }

    // Get stored code from session
    $stored_code = isset($_SESSION['authora_verification_code']) ? (string)$_SESSION['authora_verification_code'] : '';
    $stored_mobile = isset($_SESSION['authora_verification_mobile']) ? $_SESSION['authora_verification_mobile'] : '';
    $stored_time = isset($_SESSION['authora_verification_time']) ? (int)$_SESSION['authora_verification_time'] : 0;
    
    // Debug information
    error_log('Verifying code:');
    error_log('Mobile: ' . $mobile);
    error_log('Entered Code: ' . $code);
    error_log('Stored Code: ' . $stored_code);
    error_log('Stored Mobile: ' . $stored_mobile);
    error_log('Session ID: ' . session_id());
    error_log('Session Data: ' . print_r($_SESSION, true));
    
    // Check if code is expired (15 minutes)
    if (time() - $stored_time > 15 * 60) {
        wp_send_json_error(['message' => 'کد تایید منقضی شده است']);
        return;
    }

    // Check if mobile numbers match
    if ($stored_mobile !== $mobile) {
        wp_send_json_error(['message' => 'شماره موبایل نامعتبر است']);
        return;
    }

    // Check if code matches
    if ((string)$stored_code !== (string)$code) {
        wp_send_json_error(['message' => 'کد تایید نامعتبر است']);
        return;
    }

    try {
        // Find user by mobile number
        $user = get_user_by('login', $mobile);
        if (!$user) {
            // Create new user if doesn't exist
            $user_id = wp_create_user($mobile, wp_generate_password(), $mobile . '@example.com');
            if (is_wp_error($user_id)) {
                wp_send_json_error(['message' => 'خطا در ایجاد کاربر: ' . $user_id->get_error_message()]);
                return;
            }
            $user = get_user_by('id', $user_id);
        }

        // Log in user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        // Clear session data
        unset($_SESSION['authora_verification_code']);
        unset($_SESSION['authora_verification_mobile']);
        unset($_SESSION['authora_verification_time']);

        wp_send_json_success([
            'message' => 'ورود موفقیت‌آمیز',
            'redirect' => home_url()
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'خطا در ورود: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_nopriv_authora_verify_code', 'authora_handle_verify_code');
add_action('wp_ajax_authora_verify_code', 'authora_handle_verify_code');

// Modify WooCommerce login form
function authora_modify_woo_login_form() {
    if (get_option('authora_enable_woo_mobile_login') !== '1') {
        return;
    }

    // The styles and scripts are now enqueued in inc/enqueue.php
    // HTML structure for the WooCommerce login form
    ?>
    <div class="authora-mobile-login">
        <p class="message">ورود با شماره موبایل</p>
        <p class="form-row">
            <label for="authora_mobile">شماره موبایل</label>
            <input type="text" name="authora_mobile" id="authora_mobile" class="input-text" value="" required>
        </p>
        <p class="form-row">
            <button type="button" name="authora_send_code" id="authora_send_code" class="button">ارسال کد تایید</button>
        </p>
        <div class="authora-verification-code">
            <p class="form-row">
                <label for="authora_verification_code">کد تایید</label>
                <input type="text" name="authora_verification_code" id="authora_verification_code" class="input-text" value="" required maxlength="5">
            </p>
        </div>
    </div>
    <?php
}
add_action('woocommerce_login_form', 'authora_modify_woo_login_form'); 