<?php

// Modify WordPress login form
function authora_modify_login_form() {
    if (get_option('authora_enable_mobile_login') !== '1') {
        return;
    }

    // Add custom styles
    ?>
    <style>
        .authora-form-container {
            position: relative;
        }
    </style>

    <script type="text/javascript">
    window.addEventListener('load', function() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        jQuery(document).ready(function($) {
            // Store mobile number in a data attribute on the form
            var $mobileLogin = $('<div class="login">' +
                '<form name="loginform" id="loginform" action="login" method="post" data-mobile="">' +
                    '<p class="message">ورود با شماره موبایل</p>' +
                    '<div class="authora-form-container">' +
                        '<div class="authora-mobile-form">' +
                            '<p>' +
                                '<label for="authora_mobile">شماره موبایل</label><br>' +
                                '<input type="text" name="authora_mobile" id="authora_mobile" class="input" value="" size="20" required>' +
                            '</p>' +
                            '<p class="submit">' +
                                '<input type="button" name="authora_send_code" id="authora_send_code" class="button button-primary" value="ارسال کد تایید">' +
                            '</p>' +
                        '</div>' +
                    '</div>' +
                '</form>' +
            '</div>');

            $('#loginform').replaceWith($mobileLogin);

            // Handle send code button click
            $(document).on('click', '#authora_send_code', function(e) {
                e.preventDefault();
                var mobile = $('#authora_mobile').val();
                if (!mobile) {
                    alert('لطفا شماره موبایل را وارد کنید');
                    return;
                }

                // Store mobile number in form data attribute
                $('#loginform').data('mobile', mobile);

                var formData = new FormData();
                formData.append('action', 'authora_send_verification_code');
                formData.append('mobile', mobile);
                formData.append('nonce', '<?php echo wp_create_nonce("authora_send_code"); ?>');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Replace mobile form with verification form
                            $('.authora-form-container').html(
                                '<div class="authora-verification-form">' +
                                    '<p>' +
                                        '<label for="authora_verification_code">کد تایید</label><br>' +
                                        '<input type="text" name="authora_verification_code" id="authora_verification_code" class="input" value="" size="20" required maxlength="5">' +
                                    '</p>' +
                                '</div>'
                            );
                            $('#authora_verification_code').focus();
                            alert('کد تایید به شماره موبایل شما ارسال شد');
                        } else {
                            alert(response.data.message || 'خطا در ارسال کد تایید');
                        }
                    }
                });
            });

            // Auto submit when verification code is entered
            $(document).on('input', '#authora_verification_code', function() {
                var code = $(this).val();
                if (code.length === 5) {
                    // Get mobile number from form data attribute
                    var mobile = $('#loginform').data('mobile');
                    
                    var formData = new FormData();
                    formData.append('action', 'authora_verify_code');
                    formData.append('mobile', mobile);
                    formData.append('code', code);
                    formData.append('nonce', '<?php echo wp_create_nonce("authora_verify_code"); ?>');

                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.data.redirect;
                            } else {
                                alert(response.data.message || 'کد تایید نامعتبر است');
                            }
                        }
                    });
                }
            });
        });
    });
    </script>
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

    // Add custom styles
    ?>
    <style>
        .authora-mobile-login {
            display: block;
        }
        .authora-regular-login {
            display: none !important;
        }
        .authora-verification-code {
            display: none;
        }
        .authora-verification-code.active {
            display: block;
        }
        .authora-error {
            color: #dc3232;
            margin: 5px 0;
        }
        .authora-success {
            color: #46b450;
            margin: 5px 0;
        }
        .woocommerce-LostPassword {
            display: none !important;
        }
    </style>

    <script type="text/javascript">
    window.addEventListener('load', function() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        jQuery(document).ready(function($) {
            console.log('Authora login form initialized');

            // Hide regular login fields
            $('form.woocommerce-form-login .form-row:not(.form-row-last)').addClass('authora-regular-login');
            
            // Add mobile login form
            var $mobileLogin = $('<div class="authora-mobile-login">' +
                '<p class="message">ورود با شماره موبایل</p>' +
                '<p class="form-row">' +
                    '<label for="authora_mobile">شماره موبایل</label>' +
                    '<input type="text" name="authora_mobile" id="authora_mobile" class="input-text" value="" required>' +
                '</p>' +
                '<p class="form-row">' +
                    '<button type="button" name="authora_send_code" id="authora_send_code" class="button">ارسال کد تایید</button>' +
                '</p>' +
                '<div class="authora-verification-code">' +
                    '<p class="form-row">' +
                        '<label for="authora_verification_code">کد تایید</label>' +
                        '<input type="text" name="authora_verification_code" id="authora_verification_code" class="input-text" value="" required maxlength="5">' +
                    '</p>' +
                '</div>' +
            '</div>');

            $('form.woocommerce-form-login').prepend($mobileLogin);

            // Handle send code button click
            $(document).on('click', '#authora_send_code', function(e) {
                e.preventDefault();
                console.log('Send code button clicked');

                var mobile = $('#authora_mobile').val();
                if (!mobile) {
                    alert('لطفا شماره موبایل را وارد کنید');
                    return;
                }

                // Store mobile number in form data attribute
                $('form.woocommerce-form-login').data('mobile', mobile);

                var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
                var nonce = '<?php echo wp_create_nonce("authora_send_code"); ?>';

                console.log('Sending verification code to:', mobile);
                console.log('AJAX URL:', ajaxUrl);
                console.log('Nonce:', nonce);

                var formData = new FormData();
                formData.append('action', 'authora_send_verification_code');
                formData.append('mobile', mobile);
                formData.append('nonce', nonce);

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('AJAX response:', response);
                        if (response.success) {
                            $('.authora-verification-code').addClass('active');
                            $('#authora_verification_code').focus();
                            alert('کد تایید به شماره موبایل شما ارسال شد');
                        } else {
                            alert(response.data.message || 'خطا در ارسال کد تایید');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        console.error('XHR:', xhr);
                        alert('خطا در ارسال درخواست');
                    }
                });
            });

            // Auto submit when verification code is entered
            $(document).on('input', '#authora_verification_code', function() {
                var code = $(this).val();
                if (code.length === 5) {
                    // Get mobile number from form data attribute
                    var mobile = $('form.woocommerce-form-login').data('mobile');
                    
                    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var nonce = '<?php echo wp_create_nonce("authora_verify_code"); ?>';

                    console.log('Verifying code for:', mobile);
                    console.log('AJAX URL:', ajaxUrl);
                    console.log('Nonce:', nonce);

                    var formData = new FormData();
                    formData.append('action', 'authora_verify_code');
                    formData.append('mobile', mobile);
                    formData.append('code', code);
                    formData.append('nonce', nonce);

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            console.log('AJAX response:', response);
                            if (response.success) {
                                window.location.href = response.data.redirect;
                            } else {
                                alert(response.data.message || 'کد تایید نامعتبر است');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', error);
                            console.error('Status:', status);
                            console.error('Response:', xhr.responseText);
                            console.error('XHR:', xhr);
                            alert('خطا در ارسال درخواست');
                        }
                    });
                }
            });
        });
    });
    </script>
    <?php
}
add_action('woocommerce_login_form', 'authora_modify_woo_login_form'); 