<?php

use App\core\Frontend_Controller;
use Telegram\Bot\Api;
use App\modules\bot\commands;

class Cpadmin_telegram extends Frontend_Controller {

	public $telegram;

	private $chat_ids = [
		'toni' => 233934050,

	];

	public function __construct()
	{
		parent::__construct();

		$this->output->enable_profiler(false);

		$this->load->config('bot/telegram');

		$this->telegram = new Api('380251891:AAGmAFICKlZ39ACSvaHMsAp-K-pG_nz_2bM');

		// register commands
		$this->telegram->addCommands([
			commands\CpadminStartCommand::class,
			// commands\HelpCommand::class,
			// commands\RevenueTodayCommand::class,
			// commands\RevenueMonthCommand::class,
			// commands\SubscribersTodayCommand::class,
			// commands\SubscribersMonthCommand::class,
		]);
	}

	public function index()
	{
		$response = $this->telegram->getMe();
		print_r($response);
	}

	public function webhook()
	{
		$update = $this->telegram->commandsHandler(true);
	}

	public function send($chat_id)
	{
		$response = $this->telegram->sendMessage([
			'chat_id' => 233934050,
			'text' => 'Makan ga?'
		]);
	}

	public function setWebhook()
	{
		$response = $this->telegram->setWebhook(['url' => 'https://6dd6d805.ngrok.io/bot/codepolitan_telegram/webhook']);
	}

	public function handle()
	{
		$payload = json_decode($this->input->raw_input_stream, true);

		if($payload){
			$response = $this->telegram->sendMessage([
				'chat_id' => config_item('chat_ids')['group_notif'],
				'parse_mode' => 'Markdown',
				'text' => "*[Error]* ".$payload['event']['title'] ."\n"."Location: `".$payload['event']['culprit'] ."`\n"
			]);
		}

	}

	public function tes_error()
	{
		throw new Exception("Contoh error");
	}

}