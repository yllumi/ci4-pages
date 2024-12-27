<?php

use App\core\Backend_Controller;

class Role extends Backend_Controller 
{
	public function __construct()
	{
		parent::__construct();
		
		setlocale(LC_TIME, "id_ID.utf8", "id_ID", "id");
		date_default_timezone_set("Asia/Jakarta");

		$this->shared['submodule'] = 'user_role';
	}

	public function index($pagenum = 1)
	{
		checkPermission('user:show_role'); 

		$data['page_title'] = 'User Roles';

		$perpage = 10;
		$total_rows = $this->Role_model->setFilter()->count_rows();
		$uri     = 'admin/user/role/index/';

		$data['fields'] = $this->Role_model->fields;
		$data['results'] = $this->Role_model->setFilter()
								->order_by('id', 'asc')
								->paginate($perpage, $total_rows, $pagenum, $uri);
		$data['pagination'] = $this->Role_model->all_pages;

		$this->view('admin/role', $data);
	}

	public function add()
	{
		checkPermission('user:add_role');

		$data['page_title'] = 'New Role Data';
		$data['form_type']  = 'new';
		$data['action_url'] = site_url('admin/user/role/insert');

		$this->view('admin/role_form', $data);
	}

	public function edit($id)
	{
		checkPermission('user:edit_role');

		$data['page_title'] = 'Edit Role Data';
		$data['form_type']  = 'edit';
		$data['result']     = $this->Role_model->get($id);
		$data['action_url'] = site_url('admin/user/role/update/'.$id);

		$this->view('admin/role_form', $data);
	}

	public function insert()
	{
		checkPermission('user:add_role');

        // Add this if you want to override post data
        // $post = $this->input->post(null, true);
        // $this->Role_model->set_form_data($post);

		$this->Role_model->validate();
		$id = $this->Role_model->insert();

		if (!$id){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
			redirect(getenv('HTTP_REFERER'));
		} 

		$this->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
		redirect('admin/user/role');
	}

	public function update($id)
	{
		checkPermission('user:edit_role');

        // Add this if you want to override post data
        // $post = $this->input->post(null, true);
        // $this->Role_model->set_form_data($post);

		$this->Role_model->where('id', $id);
		$this->Role_model->validate('update');
		$update = $this->Role_model->update();

		if (!$update){
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
			redirect(getenv('HTTP_REFERER'));
		} 

		$this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
		redirect('admin/user/role');
	}

	public function delete($id)
	{
		checkPermission('user:delete_role');

		$status = $this->Role_model->delete($id);

		if ($status) 
			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted Successfully.</div>');
		else
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete.</div>');

		redirect('admin/user/role');
	}

	public function privileges($role_id)
	{
		checkPermission('user:manage_privileges');

		$data['page_title'] = 'Manage Role Privileges';
		
		$data['roles'] = $this->Role_model->as_dropdown('role_name')->get_all();
		
		// Delete super role
		unset($data['roles'][1]);

		$data['module_privileges'] = $this->ci_auth->getModulePrivileges('all');
		$data['entry_privileges'] = $this->ci_auth->getEntryPrivileges('all');
		
		$data['role_privileges'] = $this->Role_model->getRolePrivileges($role_id, true);
		$data['role_id'] = $role_id;

		$this->view('admin/role_privileges_form', $data);    
	}

	public function update_role_privileges()
	{
		checkPermission('user:manage_privileges');

		$post = $this->input->post();

		$update = $this->Role_model->updateRolePrivileges($post['role_id'], $post['privileges'] ?? '');

		if ($update) 
			$this->session->set_flashdata('message', '<div class="alert alert-success">Privileges updated.</div>');
		else
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Privileges failed to update.</div>');

		redirect('admin/user/role/privileges/' . $post['role_id']);
	}
}
