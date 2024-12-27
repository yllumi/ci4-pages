<?php namespace App\modules\notifier\libraries;

class WASenderDriver extends BaseDriver {

    public $endpoint;

    public function initialize($config = [])
	{
		if (empty($config)) {
			$this->addToQueue = setting_item('notifier.send_by_queue') == 'yes' ? true : false;
			$this->endpoint = rtrim(setting_item('notifier.wasender_url'), '/') . '/';
		} else {
            $this->addToQueue = $config['queue'];
            $this->endpoint = $config['endpoint'];
        }
	}

    public function sendText($to, $message)
    {
        // Send WA using Queue
        if ($this->addToQueue == true) 
        {
            $jobdata = [
                'driver' => 'WASender',
                'endpoint' => $this->endpoint,
                'to' => $to,
                'message' => $message
            ];

            \App\libraries\Beanstalk::produce('notifier', $jobdata);
        }

        // Send WA directly
        else {
            return $this->sendRequest('send', [
                'to'      => $to,
                'message' => $message
            ]);
        }
    }

    public function sendRequest($method, $data = null)
    {
        $curl = new \Curl\Curl();
        $res = $curl->post($this->endpoint.$method, $data);
        $responseArray = json_decode($res->response, true);
        if(($responseArray['status'] ?? false) == true)
            return ['status' => 'success', 'message' => 'Message sent.'];
        else
            return ['status' => 'failed', 'message' => $res->response];
    }

}