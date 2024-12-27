<?php namespace App\modules\notification\libraries;

/**
 *	Firebase Cloud Messaging Library
 *  Author: Toni
 */
class Fcm {

	private $endpoint;
	private $authToken;

	public function __construct()
	{
		ci()->load->config('notification/fcm');

		$this->authToken = config_item('fcm')['authToken'];
		$this->endpoint = config_item('fcm')['endpoint'];
	}

	public function push(array $data, $topic = 'articles-staging')
	{
		if(! config_item('fcm')['enable']) return false;

		$curl = new \Curl\Curl();
		$curl->setHeader('Authorization', $this->authToken);
		$curl->setHeader('Content-Type', 'application/json');

		$payload = [
			'to' => '/topics/'.$topic,
			'data' => $data
		];

		$curl->post($this->endpoint, $payload, true);

		if($curl->error) {
		    return $curl->error_code;
		} else {
		    return $curl->response;
		}
	}

}