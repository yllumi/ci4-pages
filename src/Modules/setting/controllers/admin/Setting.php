<?php

use App\core\Backend_Controller;
use Symfony\Component\Yaml\Yaml;

class Setting extends Backend_Controller {

	public function __construct()
	{
        parent::__construct();
    }
    
	public function index($module = 'site')
	{
		checkPermission('setting:show');

        $data['core_setting'] = core_setting();
        $data['modules_setting'] = module_setting();
        $data['entries_setting'] = entry_setting();
        $data['site_setting'] = site_setting();

		$all = array_merge($data['core_setting'], $data['modules_setting'], $data['entries_setting'], $data['site_setting']);
		$data['current_module'] = $module;
		$data['current_setting'] = $all[$module];
		$data['page_title'] = 'Settings &middot; '.$all[$module]['name'];
		$this->view('admin/form', $data);
	}

	public function update($module = null)
	{
		if(!$module) show_404();
		checkPermission('setting:update');

		$old_value = $this->Setting_model->getAll($module, true);
        $data = array_merge($old_value[$module] ?? [], $this->input->post($module));

        $this->Setting_model->updateBatch($module, $data);
        
        $this->session->set_flashdata('message', '<div class="alert alert-success">Sucessfully Updated.</div>');
        
		redirect('admin/setting/index/'.$module);
    }

}
