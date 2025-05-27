<?php

defined('ABSPATH') || exit;

require_once(__DIR__ . '/../SmsDriverInterface.php');

class ShahvarSMS implements SmsDriverInterface {
    private $apiKey;
    private $baseUrl = 'https://api2.ippanel.com/api/v1/sms/pattern/normal/send';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function sendVerifyCode($mobile, $code) {
        $patternCode = get_option('authora_shahvar_pattern_code');
        
        $url = $this->baseUrl;
        $data = [
            'code' => $patternCode,
            'sender' => get_option('authora_shahvar_sender_number'),
            'recipient' => $mobile,
            'variable' => [
                'number' => $code
            ]
        ];

        $response = wp_remote_post($url, [
            'headers' => [
                'accept' => '*/*',
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data),
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