<?php defined('BASEPATH') OR exit('No direct script access allowed');

class {crudname}_model extends MY_Model
{
	// Define table name
	public $table = '{table}';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	// Use soft delete (not delete row, but only mark row as deleted)
	public $soft_deletes = TRUE;

	// Define fields for form insert and update purpose
	// You can define vallidation rules here just like CodeIgniter has
	public $insert_rules = [
		'fullname' => [
			'field'=>'fullname',
			'label'=>'Full Name',
			'rules'=>'trim|required',
		],
		'email' => [
			'field'=>'email',
			'label'=>'Email',
			'rules'=>'trim|valid_email|required',
			'errors' => [
				'valid_email' => 'Email you entered is not valid'
			],
		],
		'address' => [
			'field'=>'address',
			'label'=>'Address',
		],
		'status' => [
			'field'=>'status',
			'label'=>'Status',
		]
	];

	public $update_rules = [
		'fullname' => [
			'field'=>'fullname',
			'label'=>'Full Name',
			'rules'=>'trim|required',
		],
		'email' => [
			'field'=>'email',
			'label'=>'Email',
			'rules'=>'trim|valid_email|required',
			'errors' => [
				'valid_email' => 'Email you entered is not valid',
			],
		],
		'address' => [
			'field'=>'address',
			'label'=>'Address',
		],
		'status' => [
			'field'=>'status',
			'label'=>'Status',
		]
	];

	// Constructor
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Set filter using like statement
	 *
	 * @param array $filter
	 * @return array
	 */
	public function setFilter()
	{
		if($filter = $this->input->get('filter', true))
			$this->like($filter);

		return $this;
	}
}
