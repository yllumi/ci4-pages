<?php

use App\core\Frontend_Controller;

class Codepolitan_woowa extends Frontend_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('bot/Woowa');
	}

	public function set_webhook()
	{
		$result = $this->woowa->setWebhook(site_url().'bot/codepolitan_woowa/webhook');
		print_code($result);
	}

	public function unset_webhook()
	{
		$result = $this->woowa->unsetWebhook();
		print_code($result);
	}

	public function webhook()
	{
		$this->output->enable_profiler(false);

		$json = file_get_contents('php://input');
		$data = json_decode($json);

		print_r($data);
	}
}