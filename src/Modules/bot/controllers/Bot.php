<?php defined('BASEPATH') OR exit('No direct script access allowed');

// use BotMan\BotMan\Http\Curl;
use App\core\Frontend_Controller;
use Telegram\Bot\Api;

class Bot extends Frontend_Controller {

	private $bot = [
		'name' => 'kanditabot',
		'url' => 'https://t.me/kanditabot'
	];

	protected $telegram;

	public function __construct()
	{
		parent::__construct();

		$this->load->config('bot/telegram');

		if(!isLoggedIn()) redirect('user/login');

		if($_ENV['CI_ENV'] != 'production')
		{
			$this->bot = [
				'name' => 'cpbottesting_bot',
				'url' => 'https://t.me/cpbottesting_bot'
			];
		}

		$this->load->library('bot/telegrambot');
		$this->telegram = $this->telegrambot->get($this->bot['name']);
	}

	public function index()
	{
		redirect('bot/connect');
	}

	public function connect()
	{
		$this->load->model('bot/Telegram_botuser_model');

		$bot = $this->bot;
		$tg_user = $this->Telegram_botuser_model->getUser($this->shared['me']['id'], $this->bot['name']);
		
		$this->load->render('connect', compact('bot','tg_user'));
	}

	public function connecting()
	{
		$data = $this->input->get(null, true);

		try {
			if($_ENV['CI_ENV'] == 'production')
				$data = $this->_checkTelegramAuthorization($data);
	
			$this->_saveTelegramUserData($data);
	
		} catch (Exception $e) {
			die ($e->getMessage());
		}

		redirect('bot/connect');
	}

	private function _checkTelegramAuthorization($auth_data) {
		$check_hash = $auth_data['hash'];
		unset($auth_data['hash']);
		$data_check_arr = [];
		foreach ($auth_data as $key => $value) {
			$data_check_arr[] = $key . '=' . $value;
		}
		sort($data_check_arr);
		$data_check_string = implode("\n", $data_check_arr);
		$token = $this->config->config['bots'][$this->bot['name']]['token'];
		$secret_key = hash('sha256', $token, true);
		$hash = hash_hmac('sha256', $data_check_string, $secret_key);
		if (strcmp($hash, $check_hash) !== 0) {
			throw new Exception('Data is NOT from Telegram');
		}
		if ((time() - $auth_data['auth_date']) > 86400) {
			throw new Exception('Data is outdated');
		}
		return $auth_data;
	}

	private function _saveTelegramUserData($auth_data) {
		$user = $this->db->select('id')->where('id', $auth_data['id'])->get('bot_telegram_users')->row_array();
		$data = [
			'id' => $auth_data['id'],
			'user_id' => $this->shared['me']['user_id'],
			'botname' => $this->bot['name'],
			'first_name' => $auth_data['first_name'],
			'last_name' => $auth_data['last_name'] ?? '',
			'username' => $auth_data['username'] ?? '',
			'photo_url' => $auth_data['photo_url'] ?? '',
			'auth_date' => $auth_data['auth_date']
		];

		if(empty($user))
			$this->db->insert('bot_telegram_users', $data);
		else
			$this->db->where('id', $auth_data['id'])->update('bot_telegram_users', $data);

		setcookie('tg_user', $auth_data['username']);

		$messages = $this->config->config['start_greetings'];

		// Send first message
		$first_message = array_shift($messages);
		$first_message['params']['text'] = str_replace("{NAMA}", $auth_data['first_name'], $first_message['params']['text']);
		$this->telegram->{$first_message['method']}($first_message['params'] + ['chat_id' => $auth_data['id']]);

		// Send next messages
		foreach ($messages as $message) {
			$this->telegram->{$message['method']}($message['params'] + ['chat_id' => $auth_data['id']]);
		}
	}

	function tes()
    {
    	$this->load->library('bot/telegrambot');
    	$data = [
    		'message' => [
    			"Halo *{name}*, ini alamatnya: [link]({url})", 
    			[
    				'name' => 'Toni',
    				'url' => 'https://www.codepolitan.com/'
    			]
    		],
    		'photo' => 'https://m.ayobandung.com/images-bandung/post/articles/2020/01/13/76173/logo_google.jpg',
    		'disable_web_page_preview' => true
    	];
    	$response = $this->telegrambot->setAsync()->sendMessageToUser(361, $data);
    	dd($response);
    }

    function coba()
    {
    	$this->load->library('bot/woowa');
    	$res = $this->woowa->checkNumber("628125262646");
    	dd($res);
    }
}