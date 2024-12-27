<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notification
 *
 * @author Oriza
 */

class Notification_model extends MY_Model
{
	// Define table name
	public $table = 'notifs';
	public $table_recipients = 'notif_recipients';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	// Constructor
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('notification/Room_model');
    }

	public function getNotifs($user_id, $limit = 10, $offset = 0)
	{
		$result1 = $this->db->select("*, {$this->table}.id as notif_id, {$this->table}.created_at as message_date, {$this->table_recipients}.created_at as user_date")
						->from($this->table)
				 		->join($this->table_recipients, $this->table_recipients.'.notif_id = '.$this->table.'.id', 'left')
				 		->where('user_id', $user_id)
                        ->order_by($this->table.'.created_at', 'desc')
						->limit($limit, $offset)
				 		->get()
                        ->result_array();

        // We split the 'or' query into two separated queries because it is much lighter
		$result2 = $this->db->select("*, {$this->table}.id as notif_id, {$this->table}.created_at as message_date, {$this->table_recipients}.created_at as user_date")
						->from($this->table)
				 		->join($this->table_recipients, $this->table_recipients.'.notif_id = '.$this->table.'.id', 'left')
				 		->where('is_global', 1)
                        ->order_by($this->table.'.created_at', 'desc')
						->limit($limit, $offset)
				 		->get()
                        ->result_array();

        // Join two results
        $results = array_merge($result1, $result2);

        if(!$results) return [];

        $notifs = [];
        foreach ($results as $row) {
        	$rawdate = $row['user_date'] ?? $row['message_date'];
        	$rawdate = strtotime($rawdate);
        	$notifs[$rawdate] = [
	            'notif_id' => $row['notif_id'],
	            'user_id' => $row['user_id'],
	            'type' => $row['notif_type'],
	            'meta' => $row['notif_meta'],
	            'hash' => $row['hash'],
	            'meta' => json_decode($row['notif_meta'], true),
	            'is_backend' => $row['is_backend'],
	            'is_global' => $row['is_global'],
	            'rawdate' => $rawdate,
	            'date' => PHP81_BC\strftime("%d %B %Y, %H:%M %Z", $rawdate, ci()->config->item('locale')),
        	];
        }
        unset($result1, $result2, $results);
        krsort($notifs);
        $notifs = array_values($notifs);

        return $notifs;
    }
    
    public function getTotalNotifs($user_id)
	{
		return $this->db->select($this->table . '.id')
						->from($this->table)
				 		->join($this->table_recipients, $this->table_recipients.'.notif_id = '.$this->table.'.id', 'left')
				 		->where('user_id', $user_id)
				 		->or_where('is_global', 1)
				 		->where($this->table.'.created_at >=', $this->shared['me']['created_at'])
                        ->count_all_results();
	}

	public function writeNotif($type, $meta, $is_global = 1, $userids = [])
	{
		$metaStructure = [
			'message' => '',
			'uri' => '',
			'icon' => 'comment'
		];
		$meta = array_merge($metaStructure, $meta);

		$data['notif_type'] = $type;
		$data['notif_meta'] = json_encode($meta);
		$data['hash'] = sha1($data['notif_meta']);
		$data['is_global'] = $is_global;

		// Insert notif message
		if(!$notif = $this->_getByHash($data['hash']))
		{
			$this->db->insert($this->table, $data);
			$id = $this->db->insert_id();
		} else {
			$id = $notif['id'];
		}

		// Insert notif recipients
		if(!empty($userids)){
			$recipients = [];
			foreach ($userids as $userid) {
				$recipients[] = ['notif_id'=>$id,'user_id'=>$userid];
			}
			$this->_writeRecipients($recipients);
		}
	}

	public function sendNotifToTelegram()
	{
		$this->load->library('bot/botsender');
		$data = [
			'name' => 'Toni',
			'message' => 'Siap siaaaap {name}'
		];
		$this->botsender->sendMessage('cpbottesting_bot', 233934050, $data);
    }
    
    /**
     * Push Notif to Room
     * 
     * @return bool
     */
    public function push($sets)
	{
        // Masukan kedalam room.
        $this->Room_model->push($sets['room'], $sets['user_id']);
        
        // Get recipient by room.
        $recipients = $this->Room_model->getRecipients($sets['room']);
        
        $this->writeNotif($sets['type'], [
            'message' => $sets['message'],
            'uri' => $sets['uri'],
            'icon' => 'comment'
        ], 0, $recipients);
        
        return true;
    }

	private function _writeRecipients($recipients)
	{
		foreach ($recipients as $recipient) {
			$older = $this->db->where($recipient)->get($this->table_recipients)->row_array();
			if($older){
				$this->db->where($recipient)->update($this->table_recipients, ['created_at'=>date("Y-m-d H:i:s")]);
			} else {
				$this->db->insert($this->table_recipients, $recipient);
			}
		}
	}

	private function _getByHash($hash)
	{
		return $this->db->where('hash', $hash)->get($this->table)->row_array();
	}
	
}