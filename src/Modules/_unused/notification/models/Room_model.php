<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Room
 *
 * @author Oriza
 */

class Room_model extends MY_Model
{
	public $table = 'notif_rooms';
	public $protected = ['id'];
    public $soft_deletes = TRUE;
	public $fields = [
		'room' => [
			'field'=>'room',
			'label'=>'Room',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'user_id' => [
			'field'=>'user_id',
			'label'=>'User',
			'datalist' => true,
			'rules'=>'required'
		],
		'status' => [
			'field'=>'status',
            'label'=>'Status',
            'rules'=>'required'
		]
	];

	public function __construct()
	{
		parent::__construct();
    }
    
    /**
	 * Get Recipients
	 *
	 * @return array
	 */
	public function getRecipients($room)
	{
        $recipients = $this->where('room', $room)->get_all();
        $final = [];

        foreach ($recipients as $recipient) {
            if ($this->session->user_id != $recipient['user_id'])
                $final[] = $recipient['user_id'];
        }
        
        return $final;
    }

    /**
	 * Is in room?
	 *
	 * @return bool
	 */
	public function isInRoom($room, $user_id)
	{
        $user = $this->where(['room' => $room, 'user_id' => $user_id])->get();

        if (!empty($user))
            return true;
        
        return false;
    }

    /**
	 * Push
	 *
	 * @return bool
	 */
	public function push($room, $user_id)
	{
        $user = $this->where(['room' => $room, 'user_id' => $user_id])->get();
        
        if (empty($user))
            return $this->insert(['room' => $room, 'user_id' => $user_id]);
        
        return false;
    }
    
    /**
	 * Remove
	 *
	 * @return bool
	 */
	public function remove($room, $user_id)
	{
        return $this->where(['room' => $room, 'user_id' => $user_id])->delete();
    }

}
