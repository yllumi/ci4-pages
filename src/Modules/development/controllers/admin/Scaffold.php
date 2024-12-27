<?php

use App\core\Backend_Controller;

class Scaffold extends Backend_Controller {

	function __construct()
	{
		parent::__construct();

		$this->shared['submodule'] = 'scaffold';
	}

	public function index()
	{
		$data['page_title'] = "CRUD Scaffold";

		$this->view('scaffold/index', $data);
	}

}
