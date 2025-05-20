<?php
/**
 * @package Authora
 * @version 1.0.0
 */
/*
Plugin Name: Authora
Plugin URI: https://github.com/Rayiumir/Authora
Description: Easy login with mobile number for WordPress.
Author: Raymond Baghumian
Version: 1.0.0
Author URI: https://rayium.ir
*/

defined('ABSPATH') || exit;

define('AUTHORA_LOGIN_VERSION', '1.0.0');

define( 'AUTHORA_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AUTHORA_LOGIN_PATH', plugin_dir_path(__FILE__) );

define('AUTHORA_LOGIN_CSS', AUTHORA_LOGIN_URL . 'css/');
define('AUTHORA_LOGIN_JS', AUTHORA_LOGIN_URL . 'js/');
define('AUTHORA_LOGIN_IMAGES', AUTHORA_LOGIN_URL . 'img/');

define('AUTHORA_LOGIN_PUBLIC', AUTHORA_LOGIN_PATH . 'public/');
define('AUTHORA_LOGIN_VIEW', AUTHORA_LOGIN_PATH . 'view/');
define('AUTHORA_LOGIN_INC', AUTHORA_LOGIN_PATH . 'inc/');
define('AUTHORA_LOGIN_ADMIN', AUTHORA_LOGIN_PATH . 'admin/');
define('AUTHORA_LOGIN_DRIVER', AUTHORA_LOGIN_PATH . 'drivers/');
 
 
 
// Calling Files
 
require(AUTHORA_LOGIN_PUBLIC . 'modal.php');
require(AUTHORA_LOGIN_INC . 'enqueue.php');
require(AUTHORA_LOGIN_INC . 'ajax.php');
require(AUTHORA_LOGIN_INC . 'activation.php');
require(AUTHORA_LOGIN_INC . 'functions.php');
require(AUTHORA_LOGIN_DRIVER . 'SMSIR/Smsir.php');
require(AUTHORA_LOGIN_DRIVER . 'SmsDriverInterface.php');
require(AUTHORA_LOGIN_DRIVER . 'SmsManager.php');

if(is_admin())
{
    require(AUTHORA_LOGIN_ADMIN . 'manager.php');
}

// Activation and Deactivation Tables

global $wpdb;
$wpdb->authora_login = $wpdb->prefix . 'authora_login';

register_activation_hook( __FILE__, 'authora_activation' );
register_deactivation_hook( __FILE__, 'authora_activation' );