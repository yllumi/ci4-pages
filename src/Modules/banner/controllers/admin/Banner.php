<?php

use App\core\Backend_Controller;

class Banner extends Backend_Controller {

    protected $attribute = []; 

	public function __construct()
	{
		parent::__construct();
        
        $this->load->library('pagination');
        $this->load->model('Banner_model');

        // Define attribute
        $this->attribute = [
            'caption' => 'Banner',
            'list_template' => 'admin/banner/list',
            'form_template' => 'admin/banner/form',
            'base_url' => site_url('admin/banner'),
            'insert_url' => site_url('admin/banner/insert'),
            'update_url' => site_url('admin/banner/update')
        ];
	}

	public function index($status = 'all')
	{
		$data['page_title'] = $this->attribute['caption'];
		$data['total'] = $this->Banner_model->getBanners('total', $status);
		
		$config['base_url'] = $this->attribute['base_url'] . '/index/' . $status;
		$config['total_rows'] = $data['total'];
		$config['per_page'] = 10;
		$config['uri_segment'] = 5;
		
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->Banner_model->getBanners('data', $status, $config['per_page'], $this->uri->segment(5));
        $data['attribute'] = $this->attribute;
        
		$this->view($this->attribute['list_template'], $data);
	}

	public function search($status = 'all')
	{
        $post = $this->input->post();
        
		$config = [
            'base_url' => $this->attribute['base_url'] . '/index/' . $status,
		    'total_rows' => $this->Banner_model->searchSample('total', $status, $post['keyword']),
		    'per_page' => 10,
            'uri_segment' => 5
        ];
        
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->Banner_model->searchSample('data', $status, $post['keyword'], $config['per_page'], $this->uri->segment(5));
        $data['keyword'] = $post['keyword'];
        $data['attribute'] = $this->attribute;
        $data['page_title'] = 'Search ' . $this->attribute['caption'];
        
		$this->view($this->attribute['list_template'], $data);
    }
    
    public function add()
	{
	 	$data['page_title'] = 'New ' . $this->attribute['caption'];
		$data['form_type'] = 'new';
        $data['action_url'] = $this->attribute['insert_url'];
        $data['attribute'] = $this->attribute;

		$this->view($this->attribute['form_template'], $data);
    }
    
    public function edit($id)
	{
		$data['page_title'] = 'Edit ' . $this->attribute['caption'];
		$data['form_type'] = 'edit';
		$data['result'] = $this->Banner_model->getBanner('id', $id);
        $data['action_url'] = $this->attribute['update_url'];
        $data['attribute'] = $this->attribute;
        
		$this->view($this->attribute['form_template'], $data);
    }
    
    public function insert()
	{
        $post = $this->input->post();

        foreach($post as $p => $value)
            $this->session->set_flashdata($p, $value);
        
        $insert = $this->Banner_model->insertBanner([
            'placing' => $post['placing'],
            'name' => $post['name'],
            'source' => $post['source'],
            'status' => $post['status'],
            'start' => $post['start'],
            'end' => $post['end'],
            'client' => $post['client']
        ]);
        
        if ($insert['status'] == 'success') 
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">'. $insert['message'] . '</div>');
            redirect($this->attribute['base_url'] . '/edit/' . $insert['id']);
        }
        
        $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $insert['message'] .'</div>');
        redirect($this->attribute['base_url'] . '/add');
    }
    
    public function update()
	{
        $post = $this->input->post();
        
		$update = $this->Banner_model->updateBanner(['id' => $post['id']], [
			'id' => $post['id'],
			'placing' => $post['placing'],
			'name' => $post['name'],
            'source' => $post['source'],
            'status' => $post['status'],
            'start' => $post['start'],
            'end' => $post['end'],
            'client' => $post['client']
        ]);
        
        // Redirect
        if ($update['status'] == 'success') 
		    $this->session->set_flashdata('message', '<div class="alert alert-success">'. $update['message'] .'</div>');
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. $update['message'] .'</div>');
        
		redirect($this->attribute['base_url'] . '/edit/' . $post['id']);
	}
}
