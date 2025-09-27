<?php

defined('ABSPATH') || exit;

require_once(__DIR__ . '/../SmsDriverInterface.php');

class AuthoraSmsIrDriver implements AuthoraSmsDriverInterface {
    
    protected $apiKey;
    protected $templateId;

    public function __construct($apiKey, $templateId) {
        $this->apiKey = $apiKey;
        $this->templateId = $templateId;
    }

    public function sendVerifyCode($mobile, $code) {
        $params = [
            'Mobile' => $mobile,
            'TemplateId' => $this->templateId,
            'Parameters' => [
                ['Name' => 'CODE', 'Value' => $code],
                ['Name' => 'DOMAIN', 'Value' => '@' . sanitize_text_field($_SERVER['HTTP_HOST'] ?? '')],
                ['Name' => 'OTP_CODE', 'Value' => '#' . $code],
            ]
        ];

        $response = wp_remote_post('https://api.sms.ir/v1/send/verify', [
            'headers' => [
                'Content-Type' => 'application/json',
                'ACCEPT' => 'application/json',
                'X-API-KEY' => $this->apiKey
            ],
            'body' => json_encode($params)
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response));

        if ($body->status != 1) {
            return new WP_Error('sms_send_error', 'خطا در ارسال پیامک از طریق SMS.ir');
        }

        return $body->data;
    }
}