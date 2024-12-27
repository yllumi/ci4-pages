<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Variable_model extends MY_Model
{
	// Define table name
	public $table = 'mein_variables';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	// Define fields for form insert and update purpose
	// You can define validation rules here just like CodeIgniter has
	public $fields = [
		'variable' => [
			'field'=>'variable',
			'label'=>'Variable Name',
			'datalist' => true,
			'rules'=>'trim|strtolower|alpha_dash|required',
		],
		'value' => [
			'field'=>'value',
			'label'=>'Variable Value',
			'datalist' => true,
			'rules'=>'trim',
		],
	];

	// Constructor
	public function __construct()
	{
		parent::__construct();
	}

	public function getItem($variable)
	{
		$this->db->select('value');
		$this->db->from($this->table);
		$this->db->where('variable', $variable);
		
		$row = $this->db->get()->row();

		if (!empty($row))
			return $row->value;

		return null;
	}
	
}