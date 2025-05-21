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
    </style>
    <div class="wrap">
        <h1>تنظیمات پیامک Authora</h1>
        <form method="post" action="options.php">
            <?php settings_fields('authora_sms_settings'); ?>
            <?php do_settings_sections('authora_sms_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">اپراتور پیامک</th>
                    <td>
                        <select name="authora_sms_driver">
                            <option value="smsir" <?php selected($selected_driver, 'smsir'); ?>>SMS.ir</option>
                            <option value="farazsms" <?php selected($selected_driver, 'farazsms'); ?>>فراز اس‌ام‌اس</option>
                        </select>
                    </td>
                </tr>
            </table>

            <div class="smsir-settings" style="display: <?php echo $selected_driver === 'smsir' ? 'block' : 'none'; ?>">
                <h3>تنظیمات SMS.IR</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">API Key</th>
                        <td><input type="text" name="authora_smsir_api_key" value="<?php echo esc_attr($smsir_api_key); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Template ID</th>
                        <td><input type="number" name="authora_smsir_template_id" value="<?php echo esc_attr($smsir_template_id); ?>" class="regular-text" /></td>
                    </tr>
                </table>
            </div>

            <div class="farazsms-settings" style="display: <?php echo $selected_driver === 'farazsms' ? 'block' : 'none'; ?>">
                <h3>تنظیمات فراز اس‌ام‌اس</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">API Key</th>
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
    <?php
}