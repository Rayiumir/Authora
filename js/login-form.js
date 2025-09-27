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
            formData.append('nonce', authora_login_form.nonce_send_code);

            $.ajax({
                url: authora_login_form.ajax_url,
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
                formData.append('nonce', authora_login_form.nonce_verify_code);

                $.ajax({
                    url: authora_login_form.ajax_url,
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

// WooCommerce login form modification
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

            var ajaxUrl = authora_login_form.ajax_url;
            var nonce = authora_login_form.nonce_send_code;

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

                var ajaxUrl = authora_login_form.ajax_url;
                var nonce = authora_login_form.nonce_verify_code;

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