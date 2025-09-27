<?php

defined('ABSPATH') || exit;

require_once(__DIR__ . '/../SmsDriverInterface.php');

class AuthoraFarazSMS implements AuthoraSmsDriverInterface {
    
    protected $apiKey;
    protected $patternCode;
    protected $senderNumber;
    protected $baseUrl = 'https://api2.ippanel.com/api/v1/sms/pattern/normal/send';

    public function __construct() {
        $this->apiKey = get_option('authora_farazsms_api_key');
        $this->patternCode = get_option('authora_farazsms_pattern_code');
        $this->senderNumber = get_option('authora_farazsms_sender_number');
    }

    public function sendVerifyCode($mobile, $code) {
        $mobile = preg_replace('/^0/', '+98', $mobile);

        $params = [
            'code' => $this->patternCode,
            'sender' => $this->senderNumber,
            'recipient' => $mobile,
            'variable' => [
                'verification-code' => $code
            ]
        ];

        $response = wp_remote_post($this->baseUrl, [
            'headers' => [
                'accept' => 'application/json',
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($params),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            error_log('FarazSMS Connection Error: ' . $response->get_error_message());
            return new WP_Error('sms_connection_error', 'مشکل در ارتباط با سرور پیامکی: ' . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            error_log('FarazSMS Empty Response');
            return new WP_Error('sms_empty_response', 'پاسخ خالی از سرور فراز اس ام اس');
        }

        error_log('FarazSMS Raw Response: ' . $body);

        $result = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = json_last_error_msg();
            error_log('FarazSMS JSON Error: ' . $error_message . ' - Response: ' . $body);
            return new WP_Error('sms_json_error', 'خطا در پردازش پاسخ سرور فراز اس ام اس: ' . $error_message);
        }

        if (!isset($result['code']) || $result['code'] != 200) {
            $error_message = isset($result['message']) ? $result['message'] : 'خطای نامشخص';
            error_log('FarazSMS API Error: ' . print_r($result, true));
            return new WP_Error('sms_send_error', 'ارسال پیامک ناموفق بود: ' . $error_message);
        }

        return [
            'success' => true,
            'message' => 'کد OTP ارسال شد'
        ];
    }
} 