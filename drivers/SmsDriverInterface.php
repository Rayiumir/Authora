<?php

if (!interface_exists('SmsDriverInterface')) {
    interface SmsDriverInterface {
        public function sendVerifyCode($mobile, $code);
    }
}