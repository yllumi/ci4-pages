<?php

namespace App\modules\bot\libraries;

class Woowa {

	protected $async = false;
	protected $asyncPriority = 9;
	protected $delay = 0;
	protected $retryAfter = false;

	function __construct()
	{
		$this->ci = &get_instance();
		$this->ci->config->load('bot/woowa');
	}

	// $data = ['anything' => 'apapun', 'message' => 'Masukkan {anything}', 'photo' => 'https://domain.com/path/to/image.jpg']
	public function sendToUser($user_id, $data = [])
	{
		if(! config_item('enable_woowa')) return die('Woowa is disabled');
		
		$ci = &get_instance();

		$ci->load->model('user/User_model');
		$user = $ci->User_model
					->fields($ci->User_model->table_profile.'.phone')
					->join_profile()
					->get($user_id);

		if(empty($user) || empty($user['phone'] ?? ''))
			$response = ['status' => 'failed', 'message' => 'User belum menginput nomor telepon.'];
		else
			$response = $this->sendMessage($user['phone'], $data);

		return $response;
	}

	public function sendMessage($target_number, $data = [])
	{
		if(! config_item('enable_woowa')) return die('Woowa is disabled');

		// Place to queue
		if($this->async) {
			$payload = $data;
			$payload['phone_number'] = $target_number;

            \App\libraries\Beanstalk::produce('woowa', $payload, $this->asyncPriority, $this->delay, $this->retryAfter);
			
            $response['status'] = 'success';
			$response['message'] = 'message queued';

			return json_encode($response);
		}

		// Parse message data
		$data['message'] = $this->prepMessage($data['message']);

		// Make sure the number begin with 62
		$phone = $this->clearNumber($target_number);

		// Setup payload to Woowa
		$payload = array(
			"key" => config_item('woowa_key'),
			"phone_no" => $phone,
			'message' => $data['message'],
		);
		$method = 'send_message';

		if($data['photo'] ?? ''){
			$payload['url'] = $data['photo'];
			$method = 'send_image_url';
		}
		
		$response = $this->_sendRequest($method, $payload);
		
		if(in_array($response, ["1","Success","success"]) || intval($response) > 0) {
			$output['message'] = $response."\n";
			$output['status'] = 'success';
		} else {
			$output['status'] = 'failed';
		}

		return json_encode($output);
	}

	public function sendMessageViaCRM($target_number, $data = [])
	{
		if(! config_item('enable_woowa')) return die('Woowa is disabled');

		// Place to queue
		if($this->async) {
			$payload = $data;
			$payload['phone_number'] = $target_number;

            \App\libraries\Beanstalk::produce('woowacrm', $payload, $this->asyncPriority, $this->delay, $this->retryAfter);
			
			$response['status'] = 'success';
			$response['message'] = 'message queued';

			return json_encode($response);
		}

		$data['deviceId'] = config_item('woowa_device_id');
		$data['number']   = $this->clearNumber($target_number);
		$data['message']  = $this->prepMessage($data['message']);

		$endpoint = "https://crm.woo-wa.com/send/message-text";

		$data_string = json_encode($data);

		$ch = curl_init($endpoint);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		$response = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($response, true);
		if(in_array($response['status'], ["1","Success","success"]) || intval($response) > 0) {
			$output['message'] = $response['message'];
			$output['status'] = 'success';
		} else {
			$output['status'] = 'failed';
		}

		return json_encode($output);
	}

	public function isNumberExists($target_number)
	{
		// Make sure the number begin with 62
		$phone = $this->clearNumber($target_number);

		// Setup payload to Woowa
		$payload = array(
			"key" => config_item('woowa_key'),
			"phone_no" => $phone,
		);
		$method = 'check_number';

		$response = $this->_sendRequest($method, $payload);
		
		if($response['message'] == "exists")
			return true;

		return false;
	}

	public function checkNumber($target_number)
	{
		// Make sure the number begin with 62
		$phone = $this->clearNumber($target_number);

		// Setup payload to Woowa
		$payload = array(
			"key" => config_item('woowa_key'),
			"phone_no" => $phone,
		);
		$method = 'check_number';

		return $this->_sendRequest($method, $payload);
	}

	private function _sendRequest($method, $payload)
	{
		$url = config_item('woowa_ip')."/api/".$method;

		$data_string = json_encode($payload);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}

	public function setWebhook($url, $action = 'set')
	{
		$data["license"] = config_item('woowa_license');
		$data["no_wa"]   = config_item('woowa_sender');
		$data["url"]     = $url;
		$data["action"]  = $action;

		$url="http://api.woo-wa.com/v2.0/webhook"; 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close ($ch);

		$response = [];
		if($err) {
			$response['status'] = 'failed';
			$response['message'] .= "cURL Error #:" . $err;
		} else {
			$response = $result;
		}

		return $response;
	}

	public function clearNumber($number)
	{
		// Make sure the number begin with 62
		$phone = substr($number, 0, 1)=='0' 
		? substr_replace($number, '62', 0, 1) 
		: $number;

		if(substr($phone, 0, 1)=='8') 
			$phone = '62'.$phone;

		return $phone;
	}

	// Prepare and parse message if it is array like this:
	/* $message = [
	 *   'halo {name}',
	 *	 ['name' => 'Toni']
	 * ];
	 */
	public function prepMessage($message)
	{
		if(is_array($message))
		{
			$find = $replace = [];
			foreach ($message[1] as $key => $value) {
				$find[] = '{'.$key.'}';
				$replace[] = urldecode($value);
			}
			$message = str_replace($find, $replace, $message[0]);
		}

		return $message;
	}

	public function getWebhook()
	{
		return $this->setWebhook(null, 'get');
	}

	public function unsetWebhook()
	{
		return $this->setWebhook(null, 'unset');
	}

	public function isEnabled()
	{
		return config_item('enable_woowa');
	}

	public function setAsync()
	{
		$this->async = setting_item('bot.enable_woowa_async')=='on' ? true : false;
		return $this;
	}

	public function setPriority($priority = 9)
	{
		$this->asyncPriority = $priority;
		return $this;
	}

	public function setDelay($delay = 0)
	{
		$this->delay = $delay;
		return $this;
	}

	public function setRetryAfter($retryAfter = 0)
	{
		$this->retryAfter = $retryAfter;
		return $this;
	}
}
