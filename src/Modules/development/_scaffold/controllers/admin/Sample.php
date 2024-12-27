<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class {crudname} extends Backend_Controller 
{
	public function __construct()
	{
		parent::__construct();

        $this->load->model('{crudname}_model');
	}

	public function index($pagenum = 1)
	{
		$data['page_title'] = '{crudname} Data';
        
        $perpage = 10;
        $total_rows = $this->{crudname}_model->setFilter()->count_rows();
        $uri     = 'admin/{module}/{crudurl}/index/';

        $data['results'] = $this->{crudname}_model->setFilter()
                                ->paginate($perpage, $total_rows, $pagenum, $uri);

        $data['pagination'] = $this->{crudname}_model->all_pages;

		$this->view('{crudurl}/admin/list', $data);
	}
    
    public function add()
	{
	 	$data['page_title'] = 'New {crudname} Data';
		$data['form_type']  = 'new';
        $data['action_url'] = site_url('admin/{module}/{crudurl}/insert');

		$this->view('{crudurl}/admin/form', $data);
    }
    
    public function edit($id)
	{
		$data['page_title'] = 'Edit {crudname} Data';
		$data['form_type']  = 'edit';
		$data['result']     = $this->{crudname}_model->get($id);
        $data['action_url'] = site_url('admin/{module}/{crudurl}/update/'.$id);
        
		$this->view('{crudurl}/admin/form', $data);
    }
    
    public function insert()
    {
        // Add this if you want to override post data
        // $post = $this->input->post(null, true);
        // $this->{crudname}_model->set_form_data($post);

        $this->{crudname}_model->validate();
        $id = $this->{crudname}_model->insert();
        
        if (!$id){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
        redirect('admin/{module}/{crudurl}');
    }
    
    public function update($id)
    {
        // Add this if you want to override post data
        // $post = $this->input->post(null, true);
        // $this->{crudname}_model->set_form_data($post);

        $this->{crudname}_model->where('id', $id);
        $this->{crudname}_model->validate('update');
        $update = $this->{crudname}_model->update();
        
        if (!$update){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
        redirect('admin/{module}/{crudurl}');
    }

    public function delete($id)
    {
        $status = $this->{crudname}_model->delete($id);

        if ($status) 
            $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted Successfully.</div>');
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete.</div>');
        
        redirect('admin/{module}/{crudurl}');
    }
}
