<?php

use App\core\REST_Controller;

class Variable extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();
    	$this->load->model('variable/Variable_model');
	}

	// Detail riwayat pembayaran
	public function detail($variable)
    {
    	$result = $this->Variable_model->where('variable',$variable)->get();

    	if($result)
	    	$this->response(['status' => 'success', 'name' => $variable, 'value' => $result['value']]);
	    else
	    	$this->response(['status' => 'failed', 'message' => 'Setting item not found', 'status_code' => REST_Controller::HTTP_NOT_FOUND]);

    }

}
