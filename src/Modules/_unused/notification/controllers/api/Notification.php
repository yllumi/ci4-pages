<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();

		setlocale(LC_TIME, "id_ID.utf8", "id_ID", "id");
        date_default_timezone_set("Asia/Jakarta");

        $this->load->model('notification/Notification_model');

        $this->limit = 10;

        // Check JWT
		$this->user = $this->checkToken();
	}

	public function index()
	{
		$results = $this->Notification_model->getNotifs($this->user->user_id, $this->limit);
        
        if (empty($results)) {
        	$this->response(['status'=>'success', 'result'=>[]]);
		}

        $output = ['status'=>'success', 'result' => $results];

		return $this->response($output);
	}

	public function detail($id)
	{
		$data = [
			'id' => 1,
			'subject' => 'Karya Wisata Santri Kelas 12',
			'date' => '2019-04-27 12:00',
			'content' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reprehenderit animi dignissimos accusamus quidem nisi modi quod ex ipsum! Aut quasi tenetur cum dolorum minima porro similique numquam omnis fugiat optio?\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit. Non pariatur rerum, culpa laudantium! Consequuntur quam temporibus ipsum sequi, cumque, blanditiis ea explicabo nisi sapiente obcaecati, unde officia placeat impedit velit.",
			'read' => true,
			'url' => site_url('api/notification/1'),
		];

		return $this->response($data);
	}

}