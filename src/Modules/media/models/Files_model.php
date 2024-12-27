<?php

class Files_model extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
        
        $this->load->helper('directory');
    }

    public function read_folders($path)
    {
        $maps = directory_map($path);
        
        return $maps;
    }
}