<?php namespace App\modules\notifier\libraries;

class Sender {

	private $driver;
	private $sender;
	private $addToQueue;

	public function __construct($driver, $config = null)
	{
		$this->driver = $driver;
		$className = "\\App\\modules\\notifier\\libraries\\".ucfirst($this->driver)."Driver";
		$this->sender = new $className;

		$this->sender->initialize($config);
	}

	public function sendText($to, $message)
	{
		return $this->sender->sendText($to, $message);
	}

}
