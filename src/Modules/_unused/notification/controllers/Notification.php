<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends Frontend_Controller 
{
	public function __construct()
	{
		parent::__construct();

        $this->output->enable_profiler(false);

        if(!isLoggedIn()) show_404();

        setlocale(LC_TIME, "id_ID.utf8", "id_ID", "id");
        date_default_timezone_set("Asia/Jakarta");

        $this->load->model('notification/Notification_model');

        $this->limit = 10;
	}

	public function index($offset = 0)
	{
        $this->load->library('pagination');

        $config = [
            'base_url' => base_url('notification/index'),
            'total_rows' => $this->Notification_model->getTotalNotifs($this->session->user_id),
            'per_page' => $this->limit
        ];

        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->create_links();
        $data['results'] = $this->Notification_model->getNotifs($this->session->user_id, $this->limit, $offset);
        
        $this->load->render('log', $data);
    }

    public function markRead($notif_id)
    {
        $update = $this->Notification_model->markRead($notif_id, $this->session->user_id ?? '');
        
        if ($update) 
            echo json_encode(['status'=>'success']);
        else
            echo json_encode(['status'=>'fail']);

        exit;
    }

    public function delete($notif_id)
    {
        $status = $this->Notification_model->deleteNotif($notif_id, $this->session->user_id ?? '');

        if ($status) 
            echo json_encode(['status'=>'success']);
        else
            echo json_encode(['status'=>'fail']);

        exit;
    }
}
