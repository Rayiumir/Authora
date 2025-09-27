<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!interface_exists('AuthoraSmsDriverInterface')) {
    interface AuthoraSmsDriverInterface {
        public function sendVerifyCode($mobile, $code);
    }
}