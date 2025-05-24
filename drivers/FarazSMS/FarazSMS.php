<?php

defined('ABSPATH') || exit;

require_once(__DIR__ . '/../SmsDriverInterface.php');

class FarazSMS implements SmsDriverInterface {
    private $apiKey;
    private $baseUrl = 'https://ippanel.com/api/select';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function sendVerifyCode($mobile, $code) {
        $patternCode = get_option('authora_farazsms_pattern_code');
        $inputData = [
            "code" => $code,
            "domain" => get_site_url(),
            "otp_code" => $code
        ];

        $url = $this->baseUrl;
        $data = [
            'op' => 'pattern',
            'user' => $this->apiKey,
            'pass' => $this->apiKey,
            'fromNum' => get_option('authora_farazsms_sender_number'),
            'toNum' => $mobile,
            'patternCode' => $patternCode,
            'inputData' => $inputData
        ];

        $response = wp_remote_post($url, [
            'body' => $data,
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        return isset($result['status']) && $result['status'] == 1;
    }
} 