<?php namespace App\modules\notifier\libraries;

class ZenzivaWADriver extends BaseDriver {

    public function __construct(){
        $this->url = rtrim(setting_item('notifier.zenziva_whatsapp_url'),'/').'/';
        $this->userkey = '95b19cf602fe';
        $this->apikey = '419f72c57b358c56f4444c20';
    }

    public function sendText($to, $message)
    {
        return $this->sendRequest('sendWA', [
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