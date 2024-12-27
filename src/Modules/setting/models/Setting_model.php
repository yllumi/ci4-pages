	<?php

/**
 * Setting
 * 
 * Setting model
 * 
 * @author Oriza
 */

class Setting_model extends CI_Model
{
	protected $table = 'mein_options';

	public function __construct()
	{
		parent::__construct();
	}

	public function getAll($option_group = 'all', $as_dimensional_array = false)
	{
		if($option_group != 'all')
			$this->db->where('option_group', $option_group);

		$result = $this->db->get($this->table)->result_array();
		if(count($result) > 0){
			$data = [];
			foreach ($result as $value){
				if($as_dimensional_array)
					$data[$value['option_group']][$value['option_name']] = $value['option_value'];
				else
					$data[$value['option_group'].'.'.$value['option_name']] = $value['option_value'];
			}

			return $data;
		}

		return false;
	}
	
	public function get($option_name)
	{
		$this->db->select('option_value');
		$this->db->from($this->table);
		$this->db->where('option_name', $option_name);
		
		$result = $this->db->get()->row();

		if (!empty($result))
			return $result->option_value;
		
		return null;
	}

	public function update($option_group, $option_name, $option_value)
	{
		$this->db->select('id');
		$this->db->from($this->table);
		$this->db->where('option_group', $option_group);
		$this->db->where('option_name', $option_name);
		
		$result = $this->db->get()->row();

		if (!empty($result))
		{
			$this->db->where('option_group', $option_group);
			$this->db->where('option_name', $option_name);
			$this->db->update($this->table, ['option_value' => $option_value]);
		}
		else
		{
			$this->db->insert($this->table, ['option_group' => $option_group, 'option_name' => $option_name, 'option_value' => $option_value]);
		}

		return true;
	}

	public function updateBatch($module, $data)
	{
		$this->db->trans_start();

		// Remove previous setting data based on option_group
		$this->db->where_in('option_group', $module)->delete($this->table);

		// Insert new data
		$entry = [];
		foreach ($data as $key => $value) {
			$entry[] = [
				'option_group' => $module,
				'option_name' => $key,
				'option_value' => $value
			];
		}
		$this->db->insert_batch($this->table, $entry);

		$this->db->trans_complete();
		return $this->db->affected_rows();
	}
}
