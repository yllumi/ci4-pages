<?php namespace App\modules\notifier\libraries;

class WoowaDriver extends BaseDriver {

    public function __construct(){
        $this->key = setting_item('notifier.woowa_key');
        $this->url = rtrim(setting_item('notifier.woowa_url'),'/').'/';
    }

    public function sendText($to, $message)
    {
        return $this->sendRequest('send_message', [
            'phone_no'  => $to,
            'message'   => $message,
            'key'       => $this->key,
        ]);
    }

    public function sendRequest($method, $data = null)
    {
        $payload = json_encode($data);

        $curl = new \Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Content-Length', strlen($payload));
        $res = $curl->post($this->url.$method, $payload);
        if(strtolower($res->response) == 'success')
            return ['status' => 'success', 'message' => 'Message sent.'];
        else
            return ['status' => 'failed', 'message' => $res->response];
    }

}