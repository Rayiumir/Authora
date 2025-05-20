<?php

if (!class_exists('SmsManager')) {
    class SmsManager {
        private static $instance = null;
        private $driver = null;

        private function __construct() {}

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function setDriver($driver) {
            if (!$driver instanceof SmsDriverInterface) {
                throw new Exception('Driver must implement SmsDriverInterface');
            }
            $this->driver = $driver;
        }

        public function sendVerifyCode($mobile, $code) {
            if (!$this->driver) {
                throw new Exception('No SMS driver set');
            }
            return $this->driver->sendVerifyCode($mobile, $code);
        }
    }
} 