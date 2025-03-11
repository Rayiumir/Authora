<?php

/**
 * Plugin Name: Authora
 */

defined('ABSPATH') || exit;

const AUTHORA_LOGIN_VERSION = '1.0.0';

define( 'AUTHORA_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AUTHORA_LOGIN_PATH', plugin_dir_path(__FILE__) );

const AUTHORA_LOGIN_CSS = AUTHORA_LOGIN_URL . 'css/';
const AUTHORA_LOGIN_JS = AUTHORA_LOGIN_URL . 'js/';
const AUTHORA_LOGIN_IMAGES = AUTHORA_LOGIN_URL . 'img/';


const AUTHORA_LOGIN_PUBLIC = AUTHORA_LOGIN_PATH . 'public/';
const AUTHORA_LOGIN_VIEW = AUTHORA_LOGIN_PATH . 'view/';
const AUTHORA_LOGIN_INC = AUTHORA_LOGIN_PATH . 'inc/';
const AUTHORA_LOGIN_ADMIN = AUTHORA_LOGIN_PATH . 'admin/';

// Calling Files

require AUTHORA_LOGIN_PUBLIC . 'modal.php';