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
		$result = $this->Variable_model->where('variable', $variable)->get();

		if ($result)
			$this->response([
				'response_code'    => REST_Controller::HTTP_OK,
				'response_message' => 'success',
				'data'         	   => $result
			]);
		else
			$this->response([
				'response_code'    => REST_Controller::HTTP_NOT_FOUND,
				'response_message' => 'Setting item not found',
			]);
	}
}
