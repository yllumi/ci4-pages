<?php

use App\core\REST_Controller;

class Navigation extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['navigation/Navigation_model','navigation/Nav_area_model']);
	}

	public function index($area = '')
	{
		$navs = $this->Nav_area_model
					 ->with_navigations('order_inside:nav_order asc')
					 ->where('area_slug', $area)
					 ->get();
		if($navs)
			$this->response($navs);
		else
			$this->response("Navigation area not found", self::HTTP_NOT_FOUND);
	}
}		 
