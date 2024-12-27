<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VariableShortcode extends Shortcode 
{
    private $variables;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('variable/Variable_model');

        // Load all variables
        if($variables = $this->Variable_model->get_all())
            foreach ($variables as $value) {
                $this->variables[$value['variable']] = $value['value'];
            }
    }

    public function get()
    {
        $varname = $this->getAttribute('name');
        $default = $this->getAttribute('default', '');

        if(isset($this->variables[$varname]))
    		return $this->output($this->variables[$varname]);

        return $default;
	}   
}