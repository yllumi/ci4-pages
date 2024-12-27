<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Telegram\Bot\Api;

class Telegrambot {

	protected $ci;
	protected $telegram;
	protected $async = false;
	protected $asyncPriority = 9;
	protected $asyncExpire = false;

	public function __construct(){
		$this->ci =& get_instance();
		$this->ci->config->load('bot/telegram');
	}

	/**
	 * Send message to group.
	 * 
	 * $data['name'=>'', 'message'=>'', 'imgurl'=>'']
	 *
	 * More group setting placed in config/telegram.php
	 */
	public function sendMessageToGroup($group, $data = [])
	{
		$botname = 'heroicbit';
		
		$chat_id = $this->ci->config->config['chat_ids'][$group];
		
		return $this->sendMessage($botname, $chat_id, $data);
	}

	/*
	 *	$data['name'=>'', 'message'=>'', 'imgurl'=>'']
	 *
	 */
	public function sendMessageToUser($user_id, $data = [])
	{
		$this->ci->load->model('bot/Telegram_botuser_model');

		$botname = 'kanditabot';
		$user = $this->ci->Telegram_botuser_model->getUser($user_id, $botname);

		if(empty($user)) return false;

		return $this->sendMessage($botname, $user['id'], $data);
	}

	/*
	 *	$data['name'=>'', 'message'=>'', 'imgurl'=>'']
	 *  set $chat_id to null to send to group premium
	 */
	public function sendMessage($botname = false, $chat_id = null, $data = [])
	{
		if(!$botname) show_error('Username is not defined');
		if(!$chat_id) show_error('Chat id is not defined');

		// Place to queue
		if($this->async) {
			$payload = $data;
			$payload['chat_id'] = $chat_id;
			$payload['botname'] = $botname;

			// $queue = new App\cli\Queue;
			// $queue->placeQueue('telegram', $payload, $this->asyncPriority, $this->asyncExpire);
			$output = 'message queued';

		} else {

			$this->telegram = $this->get($botname);

			// Parse message variables
			if(is_array($data['message']))
			{
				$find = $replace = [];
				foreach ($data['message'][1] as $key => $value) {
					$find[] = '{'.$key.'}';
					$replace[] = urldecode($value);
				}
				$data['message'] = str_replace($find, $replace, $data['message'][0]);
			}

			$payload = $data;
			$payload['text'] = $payload['caption'] = $data['message'];
			$payload['chat_id'] = $chat_id;
			$payload['parse_mode'] = $data['parse_mode'] ?? 'Markdown';

			// Setup payload
			if($payload['photo'] ?? '')
				$method = 'sendPhoto';
			else
				$method = 'sendMessage';
			
			$response = $this->telegram->$method($payload);
			$output['status'] = $response->getMessageId() ? 'success' : 'failed';
			$output['message'] = $response;
		}

		return json_encode($output);
	}

	public function get($botname)
	{
		return new Api($this->ci->config->config['bots'][$botname]['token']);
	}

	public function setAsync($priority = 9, $expire_after = false)
	{
		$this->async = true;
		$this->asyncPriority = $priority;
		$this->asyncExpire = $expire_after;
		return $this;
	}

}
