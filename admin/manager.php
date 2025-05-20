<?php

// Register settings

function authora_register_sms_settings() {
    register_setting('authora_sms_settings', 'authora_sms_driver');
    register_setting('authora_sms_settings', 'authora_smsir_api_key');
    register_setting('authora_sms_settings', 'authora_smsir_template_id');
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
    $smsir_api_key = get_option('authora_smsir_api_key');
    $smsir_template_id = get_option('authora_smsir_template_id');
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
                        </select>
                    </td>
                </tr>

                <tr><th colspan="2"><hr><strong>SMS.ir</strong></th></tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td><input type="text" name="authora_smsir_api_key" value="<?php echo esc_attr($smsir_api_key); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Template ID</th>
                    <td><input type="number" name="authora_smsir_template_id" value="<?php echo esc_attr($smsir_template_id); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}