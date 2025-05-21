<?php

// Register settings

function authora_register_sms_settings() {
    register_setting('authora_sms_settings', 'authora_sms_driver');

    // SMS.IR
    register_setting('authora_sms_settings', 'authora_smsir_api_key');
    register_setting('authora_sms_settings', 'authora_smsir_template_id');

    // Farazsms
    register_setting('authora_sms_settings', 'authora_farazsms_api_key');
    register_setting('authora_sms_settings', 'authora_farazsms_pattern_code');
    register_setting('authora_sms_settings', 'authora_farazsms_sender_number');
}
add_action('admin_init', 'authora_register_sms_settings');

function authora_sms_settings_menu() {
    add_menu_page(
        'تنظیمات پیامک',
        'تنظیمات پیامک',
        'manage_options',
        'authora-sms-settings',
        'authora_sms_settings_page',
        AUTHORA_LOGIN_URL . 'icon/authora.png',
        80
    );
}
add_action('admin_menu', 'authora_sms_settings_menu');

function authora_sms_settings_page() {
    $selected_driver = get_option('authora_sms_driver', 'smsir');
    // SMS.IR
    $smsir_api_key = get_option('authora_smsir_api_key');
    $smsir_template_id = get_option('authora_smsir_template_id');

    // Farazsms
    $farazsms_api_key = get_option('authora_farazsms_api_key');
    $farazsms_pattern_code = get_option('authora_farazsms_pattern_code');
    $farazsms_sender_number = get_option('authora_farazsms_sender_number');

    ?>
    <style>
        .wp-menu-image img {
            margin-top: -5px;
        }

        /* Main container styles */
        .authora-sms-settings {
            max-width: 800px;
            margin: 20px 0;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Header styles */
        .authora-sms-settings h1 {
            color: #1d2327;
            font-size: 24px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f1;
        }

        /* Section headers */
        .authora-sms-settings h3 {
            color: #1d2327;
            font-size: 18px;
            margin: 20px 0 15px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border-right: 4px solid #2271b1;
        }

        /* Form table styles */
        .authora-sms-settings .form-table {
            margin-top: 15px;
        }

        .authora-sms-settings .form-table th {
            padding: 15px 10px 15px 0;
            width: 200px;
            color: #1d2327;
        }

        .authora-sms-settings .form-table td {
            padding: 15px 10px;
        }

        /* Input styles */
        .authora-sms-settings input[type="text"],
        .authora-sms-settings input[type="number"],
        .authora-sms-settings select {
            width: 100%;
            max-width: 400px;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .authora-sms-settings input[type="text"]:focus,
        .authora-sms-settings input[type="number"]:focus,
        .authora-sms-settings select:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            outline: none;
        }

        /* Settings sections */
        .sms-settings {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #dcdcde;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        /* Submit button styles */
        .authora-sms-settings .submit {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f1;
        }

        .authora-sms-settings .button-primary {
            background: #2271b1;
            border-color: #2271b1;
            color: #fff;
            padding: 8px 20px;
            height: auto;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .authora-sms-settings .button-primary:hover {
            background: #135e96;
            border-color: #135e96;
        }
    </style>
    <div class="wrap authora-sms-settings">
        <h1>تنظیمات پیامک Authora</h1>
        <form method="post" action="options.php">
            <?php settings_fields('authora_sms_settings'); ?>
            <?php do_settings_sections('authora_sms_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">اپراتور پیامک</th>
                    <td>
                        <select name="authora_sms_driver" id="sms-driver-select">
                            <option value="smsir" <?php selected($selected_driver, 'smsir'); ?>>SMS.ir</option>
                            <option value="farazsms" <?php selected($selected_driver, 'farazsms'); ?>>فراز اس‌ام‌اس</option>
                        </select>
                    </td>
                </tr>
            </table>

            <div id="smsir-settings" class="sms-settings" style="display: <?php echo $selected_driver === 'smsir' ? 'block' : 'none'; ?>">
                <h3>تنظیمات SMS.IR</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">API کلید</th>
                        <td><input type="text" name="authora_smsir_api_key" value="<?php echo esc_attr($smsir_api_key); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row">قالب ID</th>
                        <td><input type="number" name="authora_smsir_template_id" value="<?php echo esc_attr($smsir_template_id); ?>" class="regular-text" /></td>
                    </tr>
                </table>
            </div>

            <div id="farazsms-settings" class="sms-settings" style="display: <?php echo $selected_driver === 'farazsms' ? 'block' : 'none'; ?>">
                <h3>تنظیمات فراز اس‌ام‌اس</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">API کلید</th>
                        <td>
                            <input type="text" name="authora_farazsms_api_key" value="<?php echo esc_attr(get_option('authora_farazsms_api_key')); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">کد الگو</th>
                        <td>
                            <input type="text" name="authora_farazsms_pattern_code" value="<?php echo esc_attr(get_option('authora_farazsms_pattern_code')); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">شماره فرستنده</th>
                        <td>
                            <input type="text" name="authora_farazsms_sender_number" value="<?php echo esc_attr(get_option('authora_farazsms_sender_number')); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            function toggleSmsSettings() {
                var selectedDriver = $('#sms-driver-select').val();
                $('.sms-settings').hide();
                $('#' + selectedDriver + '-settings').show();
            }

            $('#sms-driver-select').on('change', function() {
                toggleSmsSettings();
            });

            // Initial state
            toggleSmsSettings();
        });
    </script>
    <?php
}