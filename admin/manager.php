<?php

// Register settings

function authora_register_sms_settings() {
    // SMS Settings
    // Sms.ir
    register_setting('authora_sms_settings', 'authora_sms_driver', 'sanitize_text_field');
    register_setting('authora_sms_settings', 'authora_smsir_api_key', 'sanitize_text_field');
    register_setting('authora_sms_settings', 'authora_smsir_template_id', 'intval');

    // Farazsms
    register_setting('authora_sms_settings', 'authora_farazsms_api_key', 'sanitize_text_field');
    register_setting('authora_sms_settings', 'authora_farazsms_pattern_code', 'sanitize_text_field');
    register_setting('authora_sms_settings', 'authora_farazsms_sender_number', 'sanitize_text_field');

    // Shahvar Payam
    register_setting('authora_sms_settings', 'authora_shahvar_api_key', 'sanitize_text_field');
    register_setting('authora_sms_settings', 'authora_shahvar_sender_number', 'sanitize_text_field');

    // Integration Settings (handled manually)
    // No need to register here as we handle saving manually

    // Handle settings update redirect for SMS settings
    if (isset($_POST['option_page']) && $_POST['option_page'] === 'authora_sms_settings') {
        $active_tab = isset($_POST['active_tab']) ? sanitize_text_field($_POST['active_tab']) : 'general';
        
        // Only redirect if settings were actually saved
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'authora_sms_settings-options')) {
            $redirect_url = add_query_arg(
                array(
                    'page' => 'authora-sms-settings',
                    'settings-updated' => 'true',
                    'tab' => $active_tab
                ),
                admin_url('admin.php')
            );
            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('admin_init', 'authora_register_sms_settings');

// Custom function to handle integration settings update
function authora_handle_integration_settings_save() {
    // Check if our integration form was submitted
    if (isset($_POST['authora_integration_settings_submit']) && isset($_POST['_wpnonce'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['_wpnonce'], 'authora_integration_settings_nonce')) {
            // Nonce verification failed, handle error or just return
            wp_die('Security check failed');
        }

        // Log received POST data
        error_log('Authora: Integration settings form submitted.');
        error_log('Authora: POST data: ' . print_r($_POST, true));

        // Sanitize and update mobile login setting
        $enable_mobile_login = isset($_POST['authora_enable_mobile_login']) ? '1' : '0';
        update_option('authora_enable_mobile_login', $enable_mobile_login);
        error_log('Authora: Saved authora_enable_mobile_login: ' . $enable_mobile_login);

        // Sanitize and update WooCommerce mobile login setting
        $enable_woo_mobile_login = isset($_POST['authora_enable_woo_mobile_login']) ? '1' : '0';
        update_option('authora_enable_woo_mobile_login', $enable_woo_mobile_login);
        error_log('Authora: Saved authora_enable_woo_mobile_login: ' . $enable_woo_mobile_login);

        // Redirect back to the settings page, staying on the integration tab
        $redirect_url = add_query_arg(
            array(
                'page' => 'authora-sms-settings',
                'settings-updated' => 'true',
                'tab' => 'integration'
            ),
            admin_url('admin.php')
        );
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'authora_handle_integration_settings_save');

function authora_sms_settings_menu() {
    add_menu_page(
        __('تنظیمات آتورا', 'authora'),
        __('تنظیمات آتورا', 'authora'),
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

    // Shahvar Payam
    $shahvar_api_key = get_option('authora_shahvar_api_key');
    $shahvar_sender_number = get_option('authora_shahvar_sender_number');

    // Get active tab from URL
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    ?>
    <div class="wrap authora-sms-settings">
        <h1><?php _e('تنظیمات پیامک آتورا (Authora)', 'authora'); ?></h1>

        <?php if (isset($_GET['settings-updated'])) : ?>
            <div class="authora-notice authora-notice-success">
                <?php _e('تنظیمات با موفقیت ذخیره شد.', 'authora'); ?>
            </div>
        <?php endif; ?>

        <div class="authora-tabs">
            <a href="#general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('تنظیمات اپراتور', 'authora'); ?></a>
            <a href="#integration" class="nav-tab <?php echo $active_tab === 'integration' ? 'nav-tab-active' : ''; ?>"><?php _e('یکپارچه‌سازی', 'authora'); ?></a>
        </div>

        <div id="general" class="authora-tab-content <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
            <form method="post" action="options.php" id="general-form">
                <?php 
                settings_fields('authora_sms_settings');
                do_settings_sections('authora_sms_settings');
                ?>
                <input type="hidden" name="option_page" value="authora_sms_settings">
                <input type="hidden" name="active_tab" value="general">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('اپراتور پیامک', 'authora'); ?></th>
                        <td>
                            <select name="authora_sms_driver" id="sms-driver-select">
                                <option value="smsir" <?php selected($selected_driver, 'smsir'); ?>><?php _e('SMS.ir', 'authora'); ?></option>
                                <option value="farazsms" <?php selected($selected_driver, 'farazsms'); ?>><?php _e('فراز اس‌ام‌اس', 'authora'); ?></option>
                                <option value="shahvar" <?php selected($selected_driver, 'shahvar'); ?>><?php _e('شاهوار پیام', 'authora'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <div id="smsir-settings" class="sms-settings" style="display: <?php echo $selected_driver === 'smsir' ? 'block' : 'none'; ?>">
                    <h3><?php _e('تنظیمات SMS.IR', 'authora'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('API کلید', 'authora'); ?></th>
                            <td><input type="text" name="authora_smsir_api_key" value="<?php echo esc_attr($smsir_api_key); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('قالب ID', 'authora'); ?></th>
                            <td><input type="number" name="authora_smsir_template_id" value="<?php echo esc_attr($smsir_template_id); ?>" class="regular-text" /></td>
                        </tr>
                    </table>
                </div>

                <div id="farazsms-settings" class="sms-settings" style="display: <?php echo $selected_driver === 'farazsms' ? 'block' : 'none'; ?>">
                    <h3><?php _e('تنظیمات فراز اس‌ام‌اس', 'authora'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('API کلید', 'authora'); ?></th>
                            <td>
                                <input type="text" name="authora_farazsms_api_key" value="<?php echo esc_attr($farazsms_api_key); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('کد الگو', 'authora'); ?></th>
                            <td>
                                <input type="text" name="authora_farazsms_pattern_code" value="<?php echo esc_attr($farazsms_pattern_code); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('شماره فرستنده', 'authora'); ?></th>
                            <td>
                                <input type="text" name="authora_farazsms_sender_number" value="<?php echo esc_attr($farazsms_sender_number); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="shahvar-settings" class="sms-settings" style="display: <?php echo $selected_driver === 'shahvar' ? 'block' : 'none'; ?>">
                    <h3><?php _e('تنظیمات شاهوار پیام', 'authora'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('API کلید', 'authora'); ?></th>
                            <td>
                                <input type="text" name="authora_shahvar_api_key" value="<?php echo esc_attr(get_option('authora_shahvar_api_key')); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('شماره فرستنده', 'authora'); ?></th>
                            <td>
                                <input type="text" name="authora_shahvar_sender_number" value="<?php echo esc_attr(get_option('authora_shahvar_sender_number')); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                <?php submit_button(__('ذخیره تنظیمات', 'authora')); ?>
            </form>
        </div>

        <div id="integration" class="authora-tab-content <?php echo $active_tab === 'integration' ? 'active' : ''; ?>">
            <h3><?php _e('یکپارچه‌سازی با وردپرس و ووکامرس', 'authora'); ?></h3>
            <form method="post" action="" id="integration-form">
                <?php wp_nonce_field('authora_integration_settings_nonce'); ?>
                <input type="hidden" name="active_tab" value="integration">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('ورود با شماره موبایل در فرم وردپرس', 'authora'); ?></th>
                        <td width="500px">
                            <label class="toggle-label">
                                <span class="toggle-switch">
                                    <input type="checkbox" name="authora_enable_mobile_login" value="1" <?php echo get_option('authora_enable_mobile_login') === '1' ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('ورود با شماره موبایل در فرم ووکامرس', 'authora'); ?></th>
                        <td width="500px">
                            <label class="toggle-label">
                                <span class="toggle-switch">
                                    <input type="checkbox" name="authora_enable_woo_mobile_login" value="1" <?php echo get_option('authora_enable_woo_mobile_login') === '1' ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('ذخیره تنظیمات', 'authora'), 'primary', 'authora_integration_settings_submit'); ?>
            </form>
        </div>
    </div>
    
    <?php
}