<?php

use App\core\Frontend_Controller;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Http\Curl;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\CodeIgniterCache;
use BotMan\Drivers\Telegram\TelegramDriver;
use App\modules\bot\conversations\OnboardingConversation;

class Kandita_telegram extends Frontend_Controller {

	private $bot = 'kanditabot';
	public $botman;

	public function __construct()
	{
		parent::__construct();

		$this->output->enable_profiler(false);
		$this->load->driver('cache');
		$this->config->load('bot/telegram');

		// Load the driver(s) you want to use
		DriverManager::loadDriver(TelegramDriver::class);

		if($_ENV['CI_ENV'] != 'production') $this->bot = 'cpbottesting_bot';

		// Create an instance
		$config["telegram"] = [
	    	"token" => $this->config->config['bots'][$this->bot]['token']
		];
		$this->botman = BotManFactory::create($config, new CodeIgniterCache($this->cache->file));

		// Set listeners
		$this->registerListeners();
	}

	public function setWebhook($mode = 'set')
	{
		if(!isLoggedIn()) show_404();

		$url = site_url('bot/kandita_telegram');

		$curl = new Curl();
		if($mode == 'delete')
			$result = $curl->post('https://api.telegram.org/bot'.$this->config->config['bots'][$this->bot]['token'].'/deleteWebhook');
		else
			$result = $curl->post('https://api.telegram.org/bot'.$this->config->config['bots'][$this->bot]['token'].'/setWebhook', [], ['url' => $url]);

		echo $result->getContent();
	}

	// Webhook method
	public function index()
	{
		// Start listening
		$this->botman->listen();

		// Handle custom listeners and events
		$this->handle();
	}

	// Manual handle webhook
	public function handle()
	{
		$payload = json_decode($this->input->raw_input_stream, true);
		
		// Handle new member group invitation
		if(isset($payload['message']['new_chat_members']))
			$this->sendGreetingForNewcomers($payload);
	}

	// Register Listeners
	private function registerListeners()
	{
		// $this->botman->hears('/start', function($bot) {
		//     $bot->startConversation(new OnboardingConversation);
		// });
	}

	/**
	 * API METHODS
	 *
	 * Methods below is API for sending message as this bot
	 */

	// Send greetings for newcomers
	public function sendGreetingForNewcomers($payload)
	{
		$message = $this->_getGreetingMessage();
		$names = '';

		foreach ($payload['message']['new_chat_members'] as $member)
		{
			$names .= $member['first_name'].', ';
		}

		$message = str_replace('{{NAMA}}', rtrim($names, ', '), $message);
		$chat_id = $this->config->config['chat_ids']['group_premium'];
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown']);
	}

	// Catch event from forum and send notification to member
	public function catchForumEvent()
	{
		$payload = json_decode($this->input->post('payload', true), true);
		// echo "error code = ".json_last_error_msg();

		$userid = $payload['userid'];
		$event = $payload['event'];
		$params = $payload['params'];

		$this->load->model('bot/Telegram_botuser_model');
		$this->load->model('user/User_model');
		$user = $this->User_model->get($userid);

		// print_r($params);

		// Bila ada pertanyaan baru
		if($event == 'q_post')
			$this->notifNewQuestion($user['name'], $params['title'], $params['postid']);
		
		// Bila ada jawaban baru
		elseif($event == 'a_post')
		{
			if($userid == $params['parent']['userid']) return;

			$tg_user = $this->Telegram_botuser_model->getUser($params['parent']['userid'], $this->bot);
			if(empty($tg_user)) return;

			$this->notifNewAnswer($tg_user['id'], $user['name'], $params['parent']['title'], $params['parentid']);
		}

		// Bila ada komentar baru
		elseif($event == 'c_post')
		{
			// Kirim notif ke member komentar juga
			if(isset($params['thread']) && !empty($params['thread']))
			{
				$members = [];
				foreach ($params['thread'] as $key => $thread)
				{
					// Get only if thread userid is not sender and not question owner
					if($thread['userid'] != $userid && $thread['userid'] != $params['parent']['userid'])
						$members[$thread['userid']] = $thread['userid'];
				}

				foreach ($members as $memberid)
				{
					$tg_user = $this->Telegram_botuser_model->getUser($memberid, $this->bot);
					if(empty($tg_user)) continue;

					// Kirim notif ke member komentar lain
					$this->notifNewCommentMember($tg_user['id'], $user['name'], $params['question']['title'], $params['question']['postid']);
				}

			}

			// Jika komentar dari pertanyaan
			if($params['parent']['type'] == 'Q')
			{
				if($userid == $params['parent']['userid']) return;

				$tg_user = $this->Telegram_botuser_model->getUser($params['question']['userid'], $this->bot);
				if(empty($tg_user)) return;
				
				// Kirim notif ke pemilik pertanyaan
				$this->notifNewQuestionComment($tg_user['id'], $user['name'], $params['question']['title'], $params['question']['postid']);

			// Jika komentar dari jawaban
			} else if($params['parent']['type'] == 'A') 
			{
				if($userid == $params['parent']['userid']) return;

				$tg_user = $this->Telegram_botuser_model->getUser($params['parent']['userid'], $this->bot);
				if(empty($tg_user)) return;

				// Kirim notif ke pemilik jawaban
				$this->notifNewAnswerComment($tg_user['id'], $user['name'], $params['question']['title'], $params['question']['postid']);
			}

		}

		// Bila ada jawaban yang diedit
		elseif($event == 'a_edit')
		{
			// Dont send notification if it is silent editing
			if($userid == $params['parent']['userid'] || $params['silent']) return;

			$tg_user = $this->Telegram_botuser_model->getUser($params['parent']['userid'], $this->bot);
			if(empty($tg_user)) return;

			$this->notifEditAnswer($tg_user['id'], $user['name'], $params['parent']['title'], $params['parentid']);
		}

	}

	/** 
	 *  Send notification about new question to premium group
	 *
	 * $user_name	Nama penanya
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifNewQuestion($user_name, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{PENANYA}','{LINK}'], [$subject, $user_name, $url], $this->config->config['newest_question']);

		$chat_id = $this->config->config['chat_ids']['group_premium'];
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown']);
	}

	/** 
	 * Send notification about new answer to user thread owner
	 *
	 * $userid 		id telegram user yang punya pertanyaan
	 * $answerer 	nama penjawab
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifNewAnswer($userid, $answerer, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{PENJAWAB}','{LINK}'], [$subject, $answerer, $url], $this->config->config['new_answer']);

		$chat_id = $userid;
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
	}

	/** 
	 * Send notification about edited answer to user thread owner
	 *
	 * $userid 		id telegram user yang punya pertanyaan
	 * $answerer 	nama pengedit
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifEditAnswer($userid, $answerer, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{PENJAWAB}','{LINK}'], [$subject, $answerer, $url], $this->config->config['edit_answer']);

		$chat_id = $userid;
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
	}

	/** 
	 * Send notification about new comment on question to user thread owner
	 *
	 * $userid 		id telegram user yang punya pertanyaan
	 * $commentator	nama komentator
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifNewQuestionComment($userid, $commentator, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{KOMENTATOR}','{LINK}'], [$subject, $commentator, $url], $this->config->config['new_comment_q']);

		$chat_id = $userid;
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
	}

	/** 
	 * Send notification about new comment on answer to user answerer
	 *
	 * $userid 		id telegram user yang jawab pertanyaan
	 * $commentator	nama komentator
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifNewAnswerComment($userid, $commentator, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{KOMENTATOR}','{LINK}'], [$subject, $commentator, $url], $this->config->config['new_comment_a']);

		$chat_id = $userid;
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
	}

	/** 
	 * Send notification about new comment to comment member
	 *
	 * $userid 		id telegram user yang jawab pertanyaan
	 * $commentator	nama komentator
	 * $subject 	judul pertanyaan
	 * $postid 		id pertanyaan untuk url
	 */
	public function notifNewCommentMember($userid, $commentator, $subject, $postid)
	{
		$url = 'https://www.codepolitan.com/forum/'.$postid;

		$message = str_replace(['{JUDUL}','{KOMENTATOR}','{LINK}'], [$subject, $commentator, $url], $this->config->config['new_comment_c']);

		$chat_id = $userid;
		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
	}

	// Additional API for sending messsage from outside platform
	public function postToPremiumGroup()
	{
		$message = $this->input->post('message', true) or exit('Post data required');
		$chat_id = $this->config->config['chat_ids']['group_premium'];

		$this->botman->say($message, $chat_id, TelegramDriver::class, ['parse_mode' => 'Markdown']);
	}

	// Prepare greeting message for newcomer member in premium group
	// This is used in sendGreetingForNewcomers()
	private function _getGreetingMessage()
	{
		// Get latest message number
		$messagenum = (int)$this->cache->file->get('messagenum') ?? 0;

		// Get message text
		$message = $this->config->config['kandita_greetings'][$messagenum];

		// Update message number
		$messagenum++;
		if($messagenum == count($this->config->config['kandita_greetings'])) $messagenum = 0;

		// Save to cache, yeaaah!
		$this->cache->file->save('messagenum', (string)$messagenum, 60*60*24);

		return $message;
	}

}