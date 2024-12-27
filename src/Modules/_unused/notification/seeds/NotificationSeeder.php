<?php

class NotificationSeeder extends App\libraries\Seeder {

    private $notifications_table = 'notifs';
    private $recipients_table = 'notif_recipients';
    private $rooms_table = 'notif_rooms';
    
	public function run()
	{
		$this->db->truncate($this->notifications_table);
        $this->db->truncate($this->recipients_table);
        $this->db->truncate($this->rooms_table);

        ci()->load->model('notification/Notification_model');

        ci()->Notification_model->writeNotif('welcome', [
            'message' => 'Selamat bergabung di CodePolitan. Baca panduan belajarmu disini.',
            'uri' => 'panduan-belajar',
            'icon' => 'heart'
        ]);
	}
}