<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Newsletter extends Backend_Controller {
	
    public function __construct()
	{
		parent::__construct();

        $this->load->library('pagination');
        $this->load->model('Newsletter_model');
	}
    
	public function index($status = 'all')
	{
        $data['page_title'] = 'Newsletter Member';
		$data['total'] = $this->Newsletter_model->getMembers('total', $status);
        
		$config['base_url'] = site_url('admin/newsletter/index/' . $status);
		$config['total_rows'] = $data['total'];
		$config['per_page'] = 20;
        $config['uri_segment'] = 6;
        
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->Newsletter_model->getMembers('data', $status, $config['per_page'], $this->uri->segment(6));
        
		$this->view('newsletter/admin/member/list', $data);
	}

	public function search($status = 'all')
	{
        $get = $this->input->get();
        
		$config = [
            'base_url' => site_url('admin/newsletter/member/index/' . $status),
		    'total_rows' => $this->Newsletter_model->searchMember('total', $status, $get['keyword']),
		    'per_page' => 5,
            'uri_segment' => 5
        ];
        
		$this->pagination->initialize($config);
        
        $data['page_title'] = 'Search Member';
        $data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->Newsletter_model->searchMember('data', $status, $get['keyword'], $config['per_page'], $this->uri->segment(5));
        $data['keyword'] = $get['keyword'];
        
		$this->view('newsletter/admin/member/list', $data);
    }
}
