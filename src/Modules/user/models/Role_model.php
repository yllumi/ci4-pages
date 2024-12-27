<?php

class Role_model extends MY_Model
{
	// Define table name
	public $table = 'mein_roles';
	public $table_role_privileges = 'mein_role_privileges';

	// Define field that must be protected (not insert or updated manually)
	public $protected = ['id'];

	public $fields = [
		'role_name' => [
			'field'=>'role_name',
			'label'=>'Role Name',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'role_slug' => [
			'field'=>'role_slug',
			'label'=>'Role Slug',
			'datalist' => true,
			'rules'=>'trim|required',
		],
		'status' => [
			'field'=>'status',
			'label'=>'Role Status',
			'datalist' => true,
			'rules'=>'trim|required',
			'default' => 'active',
		]
	];

	// Constructor
	public function __construct()
	{
		parent::__construct();
	}

	// Get privilege by role
	public function getRolePrivileges($role_id, $grouped = false)
	{
		$this->db->select('module,privilege')->where('role_id', $role_id);
		$data = $this->db->get($this->table_role_privileges)->result_array();

		if(!empty($data)) {
			if($grouped) {
				$result = [];
				foreach ($data as $value) {
					$result[strtolower($value['module'])][] = $value['privilege'];
				}
				return $result;
			} else
				return $data;
		}

		return [];
	}

	public function updateRolePrivileges($role_id, $privileges)
	{
		// reset all privilege for this role
		$result = $this->db->where('role_id', $role_id)->delete($this->table_role_privileges);

		if($privileges)
		{
			$data = [];
			foreach ($privileges as $privilege) {
				list($permission_module, $permission_privilege) = explode('.', $privilege);
				$data[] = [
					'role_id' => $role_id,
					'module' => $permission_module,
					'privilege' => $permission_privilege
				];
			}
			return $this->db->insert_batch($this->table_role_privileges, $data);
		}
		
		return $result;
	}
	
}