<?php

use App\core\REST_Controller;

class Setting extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();

	}

	// Detail riwayat pembayaran
	public function detail($setting_name)
    {
    	if(strpos($setting_name, '.'))
	    	$value = setting_item($setting_name, null);
	    else
	    	$value = setting_items($setting_name);

    	if(!is_null($value))
	    	$this->response(['status' => 'success', 'name' => $setting_name, 'value' => $value]);
	    else
	    	$this->response(['status' => 'failed', 'message' => 'Setting item not found', 'status_code' => REST_Controller::HTTP_NOT_FOUND]);

    }

}
