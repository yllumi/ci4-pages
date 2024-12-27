<?php

use App\core\Backend_Controller;

class Media extends Backend_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data['page_title'] = 'File Manager';

		$this->view('admin', $data);
	}
}
