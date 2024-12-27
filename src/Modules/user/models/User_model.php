<?php

class User_model extends MY_Model
{
	// Define table name
	public $table = 'mein_users';
	public $table_role = 'mein_roles';
	public $table_profile = 'mein_user_profile';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];
	
	public $fields = [
		'name' => [
			'field'=>'name',
			'label'=>'Name',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'email' => [
			'field'=>'email',
			'label'=>'Email',
			'datalist' => true,
			'rules'=>'trim|required|valid_email',
		],
		'phone' => [
			'field'=>'phone',
			'label'=>'Phone',
			'datalist' => true,
			'rules'=>'trim',
		],
		'username' => [
			'field'=>'username',
			'label'=>'Username',
			'datalist' => true,
			'rules'=>'trim|required|alpha_dash',
		],
		'password' => [
			'field'=>'password',
			'label'=>'Email',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'status' => [
			'field'=>'status',
			'label'=>'Status',
			'datalist' => true,
			'rules'=>'trim|required',
			'default' => 'inactive'
		],
		'role_id' => [
			'field'=>'role_id',
			'label'=>'Role',
			'datalist' => true,
			'rules'=>'trim|required',
			'default' => 1
		],
		'avatar' => [
			'field'=>'avatar',
			'label'=>'Avatar',
			'datalist' => true,
			'rules'=>'trim',
		],
		'url' => [
			'field'=>'url',
			'label'=>'URL',
			'datalist' => true,
			'rules'=>'trim',
		],
		'short_description' => [
			'field'=>'short_description',
			'label'=>'Short Description',
			'datalist' => true,
			'rules'=>'trim',
		],
		'token' => [
			'field'=>'token',
			'label'=>'JWT Token',
			'rules'=>'trim',
		],
		'last_login' => [
			'field'=>'last_login',
			'label'=>'Last Login',
			'datalist' => true,
			'rules'=>'trim',
		]
	];

	// Constructor
	public function __construct()
	{
		$this->has_one['role'] = array('Role_model','id','role_id');

		parent::__construct();
	}

	public function join_profile()
	{
		$this->select($this->table.'.*, '.$this->table_profile.'.*, '.$this->table.'.id');
		$this->db->join($this->table_profile, $this->table_profile.'.user_id = '.$this->table.'.id', 'left');

		return $this;
	}

	/**
	 * Get users..
	 * 
	 * @return array
	 */
	public function getUsers($perpage = 10, $pagenum = 1, $filter = [], $uri = false)
	{
		// Set constraint for total rows

		$this->_setFilter($filter);
		$total_rows = $this->set_cache('totalUsers')->count_rows();

		// Set constraint for result
		$this->_setFilter($filter);
		
		// Exclude data with role Super if logged in user is not super 
		if(isLoggedIn('role_name') != 'Super') $this->where('role_id !=', 1);
		return $this->order_by($this->table . '.created_at', 'desc')->paginate($perpage, $total_rows, $pagenum, $uri);
	}

	/**
	 * Set filter using like statement
	 *
	 * @param array $filter
	 * @return array
	 */
	public function _setFilter($filter = [])
	{
		$fields = [];
		foreach ($filter as $key => $value) {
			// use key with format filter_[field] only
			if(strpos($key, 'filter_') === 0 && !empty(trim($value))) {
				$fields[substr($key, 7)] = $value;
				$this->like(substr($key, 7), $value);
			}
		}

		return $fields;
	}

	// Check if current loggedin user is Super 
	// and can affect superuser data
	public function canAffectSuperuser($user_id)
	{
		// Check user_id's role
		$user = $this->get($user_id);
		
		if(($user['role_id'] ?? '') != 1) return true;

		if($user['role_id'] == 1 && isLoggedIn('role_name') == 'Super')
			return true;
			
		return false;
	}
}
