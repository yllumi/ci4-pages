<?php namespace App\modules\notifier\libraries;

class ZenzivaVoiceDriver extends BaseDriver {

    public function __construct(){
        $this->url = rtrim(setting_item('notifier.zenziva_voicemsg_url'),'/').'/';
        $this->userkey = setting_item('notifier.zenziva_userkey');
        $this->apikey = setting_item('notifier.zenziva_apikey');
    }

    public function sendText($to, $message)
    {
        return $this->sendRequest('sendvoice', [
            'userkey' => $this->userkey,
            'passkey' => $this->apikey,
            'to'      => $to,
            'message' => $message,
        ]);
    }

    public function sendRequest($method, $data = null)
    {
        $curl = new \Curl\Curl();
        $res = $curl->post($this->url.$method, $data);
        $response = json_decode($res->response, true);
        if($response['status'] == 1)
            return ['status' => 'success', 'message' => 'Message sent.'];
        else
            return ['status' => 'failed', 'message' => $res->response];
    }

}