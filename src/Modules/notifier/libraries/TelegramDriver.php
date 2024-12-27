<?php namespace App\modules\notifier\libraries;

class TelegramDriver extends BaseDriver {

    private $token;
    private $url;

    public function __construct(){
        $this->token = setting_item('notifier.telegram_token');
        $this->url = 'https://api.telegram.org/bot'.$this->token.'/';
    }

    public function sendText($to, $message)
    {
        return $this->sendRequest('sendMessage', [
            'chat_id' => $to,
            'text'    => $message,
        ]);
    }

    public function sendRequest($method, $data = null)
    {
        $curl = new \Curl\Curl();
        $res = $curl->post($this->url.$method, $data);
        $response = json_decode($res->response, true);
        if($response['ok'] == true)
            return ['status' => 'success', 'message' => 'Message sent.'];
        else
            return ['status' => 'failed', 'message' => $res->response];
    }

}