<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nav_area_model extends MY_Model
{
	// Define table name
	public $table = 'mein_navigation_areas';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	// Define fields for form insert and update purpose
	// You can define validation rules here just like CodeIgniter has
	public $fields = [
		'area_name' => [
			'field'=>'area_name',
			'label'=>'Area Name',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'area_slug' => [
			'field'=>'area_slug',
			'label'=>'Area Slug',
			'datalist' => true,
			'rules'=>'trim|required|alpha_dash',
		],
		'status' => [
			'field'=>'status',
			'label'=>'Status',
			'datalist' => true,
		]
	];

	public $soft_deletes = TRUE;

	// Constructor
	public function __construct()
	{
		$this->has_many['navigations'] = array('Navigation_model','area_id','id');

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
