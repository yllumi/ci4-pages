<?php

use App\core\Frontend_Controller;

class Variable extends Frontend_Controller {

	public function __construct()
	{
        parent::__construct();
    }

    public function update()
	{  
        print_r($_POST);
        
        $name = $this->input->post('name', true);
        $value = $this->input->post('value', true);
        
        $this->Theme_model->update($name, $value);
        
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Updated.']);
	}
}