<?php

if (!class_exists('AuthoraSmsManager')) {
    class AuthoraSmsManager {
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
            if (!$driver instanceof AuthoraSmsDriverInterface) {
                throw new Exception('Driver must implement AuthoraSmsDriverInterface');
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