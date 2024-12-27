<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram_botuser_model extends CI_Model
{
	// Define table name
	public $user_table = 'bot_telegram_users';

	// Constructor
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get telegram user by id and botname
	 *
	 * @param array $filter
	 * @return array
	 */
	public function getUser($id, $botname)
	{
		return $this->db->where('user_id', $id)
						->where('botname', $botname)
				 		->get($this->user_table)
				 		->row_array();
	}

	public function getUsers($botname, $fields = '*')
	{
		return $this->db->select($fields)
						->where('botname', $botname)
				 		->get($this->user_table)
				 		->result_array();
	}
}