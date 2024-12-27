<?php

use App\core\Backend_Controller;

class Bot extends Backend_Controller {

	private $botman;

	public function __construct()
	{
        parent::__construct();

        $this->output->enable_profiler(false);
        $this->config->load('bot/telegram');
	}

	public function index()
	{
		$bots = $this->config->config['bots'];
		$this->view('admin/botlist', compact('bots'));
	}

	public function send_to_premium($botname = false)
	{
		if(!$botname) show_404();
		$chat_id = $this->config->config['chat_ids']['group_premium'];

		$this->view('admin/send_to_premium', compact('botname','chat_id'));
	}

	public function broadcast_to_member($botname = false)
	{
		if(!$botname) show_404();
		$this->load->model('bot/Telegram_botuser_model');
		$members = $this->Telegram_botuser_model->getUsers($botname,'id,first_name');
		
		$this->view('admin/send_to_all_members', compact('botname','members'));
	}

	public function sendMessage($botname = false, $chat_id = false, $name = '')
	{
		$this->load->library('bot/telegrambot');

		$data['message'] = [$this->input->post('message', true), ['name' => $name]];
		$data['photo'] = $this->input->post('imgurl', true);
		
		$response = $this->telegrambot->sendMessage($botname, $chat_id, $data);
	}

	
}