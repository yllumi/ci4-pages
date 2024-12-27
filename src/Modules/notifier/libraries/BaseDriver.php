<?php namespace App\modules\notifier\libraries;

abstract class BaseDriver {

	public $addToQueue = false;

	abstract public function sendText($to, $message);

	public function initialize($config = null)
	{
		if(!$config) {
			$this->addToQueue = setting_item('notifier.send_by_queue') == 'yes' ? true : false;
		}
	}

}
