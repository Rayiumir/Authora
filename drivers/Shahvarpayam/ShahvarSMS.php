<?php

require_once(__DIR__ . '/../SmsDriverInterface.php');

class ShahvarSMS implements SmsDriverInterface {
    
    private $api_key;
    private $sender_number;

    public function __construct() {
        $this->api_key = get_option('authora_shahvar_api_key');
        $this->sender_number = get_option('authora_shahvar_sender_number');
    }

    public function sendVerifyCode($mobile, $code) {
        $text = "کد تایید شما: {$code}";
        return $this->send_sms($mobile, $text);
    }

    public function send_sms($mobile, $text) {
        $url = 'https://shahvarpayam.ir/api/send';
        
        $data = array(
            'api_key' => $this->api_key,
            'mobile' => $mobile,
            'message' => $text
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            error_log('Authora Shahvar SMS Error: Failed to send SMS');
            return false;
        }

        $response = json_decode($result, true);

        // Check if the SMS was sent successfully
        if (isset($response['status']) && $response['status'] === 'success') {
            return true;
        }

        error_log('Authora Shahvar SMS Error: ' . print_r($response, true));
        return false;
    }

    public function get_balance() {
        $url = 'https://shahvarpayam.ir/api/balance';
        
        $data = array(
            'api_key' => $this->api_key
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            error_log('Authora Shahvar Balance Error: Failed to get balance');
            return false;
        }

        $response = json_decode($result, true);

        if (isset($response['status']) && $response['status'] === 'success' && isset($response['balance'])) {
            return $response['balance'];
        }

        error_log('Authora Shahvar Balance Error: ' . print_r($response, true));
        return false;
    }
} 