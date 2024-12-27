<?php

use App\core\Backend_Controller;

class Variable extends Backend_Controller 
{
    public function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, "id_ID.utf8", "id_ID", "id");
        date_default_timezone_set("Asia/Jakarta");
        
        $this->load->model('Variable_model');
    }

    public function index($pagenum = 1)
    {
        $data['page_title'] = 'Variable Data';
        
        $perpage = 10;
        $total_rows = $this->Variable_model->setFilter()->count_rows();
        $uri     = 'admin/variable/index/';

        $data['fields'] = $this->Variable_model->fields;
        $data['results'] = $this->Variable_model->setFilter()
                                ->order_by('created_at', 'desc')
                                ->paginate($perpage, $total_rows, $pagenum, $uri);
        $data['pagination'] = $this->Variable_model->all_pages;

        $this->view('admin/index', $data);
    }
    
    public function add()
    {
        $data['page_title'] = 'New Variable Data';
        $data['form_type']  = 'new';
        $data['action_url'] = site_url('admin/variable/insert');

        $this->view('admin/form', $data);
    }
    
    public function edit($id)
    {
        $data['page_title'] = 'Edit Variable Data';
        $data['form_type']  = 'edit';
        $data['result']     = $this->Variable_model->get($id);
        $data['action_url'] = site_url('admin/variable/update/'.$id);
        
        $this->view('admin/form', $data);
    }
    
    public function insert()
    {
        // Add this if you want to override post data
        $post = $this->input->post(null, true);
        $post['variable'] = strtolower($post['variable']);
        $this->Variable_model->set_form_data($post);

        $this->Variable_model->validate();
        $id = $this->Variable_model->insert();
        
        if (!$id){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
        redirect('admin/variable');
    }
    
    public function update($id)
    {
        // Add this if you want to override post data
        $post = $this->input->post(null, true);
        $post['variable'] = strtolower($post['variable']);
        $this->Variable_model->set_form_data($post);

        $this->Variable_model->where('id', $id);
        $this->Variable_model->validate('update');
        $update = $this->Variable_model->update();
        
        if (!$update){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
        redirect('admin/variable');
    }

    public function delete($id)
    {
        $status = $this->Variable_model->delete($id);

        if ($status) 
            $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted Successfully.</div>');
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete.</div>');
        
        redirect('admin/variable');
    }
}
