<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Navigation_model extends MY_Model
{
	// Define table name
	public $table = 'mein_navigations';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	// Define fields for form insert and update purpose
	// You can define validation rules here just like CodeIgniter has
	public $fields = [
		'caption' => [
			'field'=>'caption',
			'label'=>'Link Caption',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'area_id' => [
			'field'=>'area_id',
			'label'=>'Area',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'url' => [
			'field'=>'url',
			'label'=>'URL',
			'datalist' => true,
			'rules'=>'trim',
		],
		'url_type' => [
			'field'=>'url_type',
			'label'=>'URL Type',
			'datalist' => true,
		],
		'target' => [
			'field'=>'target',
			'label'=>'URL Target',
			'datalist' => true,
		],
		'status' => [
			'field'=>'status',
			'label'=>'Status',
			'datalist' => true,
		],
		'icon' => [
			'field'=>'icon',
			'label'=>'Icon Classes',
			'rules'=>'trim',
		],
		'nav_order' => [
			'field'=>'nav_order',
			'label'=>'Navigation Order',
			'rules'=>'trim',
		],
	];

	public $soft_deletes = TRUE;

	// Constructor
	public function __construct()
	{
		$this->has_one['area'] = array('Area_model','id','area_id');

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
