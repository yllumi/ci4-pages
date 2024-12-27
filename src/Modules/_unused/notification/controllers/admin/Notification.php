<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends Backend_Controller {

    public function __construct()
	{
		parent::__construct();
        
        $this->load->library('pagination');
        $this->load->model('notification/Notification_model');
    }

	public function index()
	{
        $data['action_url'] = site_url('admin/notification/send');

		$this->view('admin/notification/form', $data);
	}

    public function send()
    {
        $post = $this->input->post();
        
        foreach($post as $key => $value)
            $this->session->set_flashdata($key, $value);

        if (!$this->input->post('confirm')) 
        {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Kamu belum menyatakan sudah ok..</div>');
            redirect('admin/notification');
        }
        
        $this->Notification_model->writeNotif('information', [
            'message' => $this->input->post('notif'),
            'uri' => $this->input->post('uri'),
            'icon' => 'comment'
        ], 1, null);

        $this->session->set_flashdata('message', '<div class="alert alert-success">Berhasil mengirim pesan!</div>');
        
        redirect('admin/notification');
    }
}
