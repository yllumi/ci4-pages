<?php defined('BASEPATH') OR exit('No direct script access allowed');

use App\core\Frontend_Controller;
use Telegram\Bot\Api;
use App\modules\bot\commands;

class Codepolitan_telegram extends Frontend_Controller {

	public $telegram;

	private $chat_ids = [
		'toni' => 233934050,
		'group_premium' => -1001295383139

	];

	public function __construct()
	{
		parent::__construct();

		$this->output->enable_profiler(false);

		// Set token for codepolitan_bot
		$this->telegram = new Api('420260116:AAFzgIuB5AJv1cr8nW1DaKlHubKpK3kGUhE');

		// register commands
		$this->telegram->addCommands([
			commands\CodepolitanStartCommand::class,
			// commands\HelpCommand::class
		]);
	}

	public function index()
	{
		// handle command message
		$update = $this->telegram->commandsHandler(true);

		$response = $this->telegram->getMe();
		print_r($response);
	}

	public function webhook()
	{
	}

	public function send($chat_id)
	{
		$response = $this->telegram->sendMessage([
			'chat_id' => $this->chat_ids[$chat_id],
			'text' => 'Halo kawan-kawan salam kenal! :D'
		]);
	}

	public function setWebhook()
	{
		$url = 'https://dddbc2d5.ngrok.io/bot/codepolitan_telegram/webhook';
		$response = $this->telegram->setWebhook(['url' => $url]);
	}

}