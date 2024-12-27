<?php

/**
 * Banner
 *
 * @author Oriza
 */

class Banner_model extends CI_Model
{
	protected $main_table = 'banner';
    
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Get detail by field.
     *
     * @param string $field
     * @param string $value
     * @return array
     */
    public function getBanner($field, $value)
    {
        $this->db->select('*');
        $this->db->from($this->main_table);
        $this->db->where($this->main_table .'.'. $field, $value);
        
        $result = $this->db->get()->row_array();

        if (!empty($result))
            return $result; 
        
        return [];
    }

	public function getBanners($result = 'data', $status = 'all', $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			$this->db->select($this->main_table . '.id');
		else
			$this->db->select('*,' . $this->main_table . '.id as user_id');
		
		$this->db->from($this->main_table);
		
		if (!empty($status) && $status != 'all')
		{
			$this->db->where($this->main_table.'.status', $status);
		}

		$this->db->where($this->main_table.'.status !=', 'deleted');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();

		$this->db->order_by($this->main_table.'.id', 'desc');
		$this->db->limit($limit, $limit_order);
		
		return $this->db->get()->result();
    }

    /**
     * Search
     *
     * @return array
     */
    public function searchSample($result = 'data', $status = 'all', $keyword = null, $limit = 10, $limit_order = 1)
	{
		if ($result == 'total')
			$this->db->select($this->main_table . '.id');
		else
			$this->db->select('*,' . $this->main_table . '.id as user_id');
		
		$this->db->from($this->main_table);
		
		$this->db->like($this->main_table . '.name', $keyword);

		if (!empty($status) && $status != 'all')
		{
			$this->db->where($this->main_table.'.status', $status);
		}

		$this->db->where($this->main_table.'.status !=', 'deleted');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();

		$this->db->order_by($this->main_table.'.id', 'desc');
		$this->db->limit($limit, $limit_order);
		
		return $this->db->get()->result();
    }

    /**
     * Insert
     *
     * Insert to DB.
     * 
     * @return bool
     */
    public function insertBanner($param)
	{
        // Dependency
        $this->load->library('form_validation');

        // Do validation.
        $this->form_validation->set_data($param);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('placing', 'Placing', 'required|is_unique[banner.placing]');
        
        if ($this->form_validation->run() == false)
		{
			return ['status' => 'failed', 'message' => validation_errors()];
		}

		$this->db->insert($this->main_table, [
            'placing' => $param['placing'],
            'name' => $param['name'],
            'source' => $param['source'],
            'status' => $param['status'],
            'start' => $param['start'],
            'end' => $param['end'],
            'client' => $param['client'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
		return ['status' => 'success', 'message' => 'Successfully added', 'id' => $this->db->insert_id()];
    }
    
    /**
     * Update
     * 
     * @return array
     */
    public function updateBanner($condition, $param)
    {
        // Dependency
        $this->load->library('form_validation');

        // Do validation.
        $this->form_validation->set_data($param);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('placing', 'Placing', 'required');
        
        if ($this->form_validation->run() == false)
		{
			return ['status' => 'failed', 'message' => validation_errors()];
        }

        $previous = $this->getBanner('id', $param['id']);

        if ($previous['placing'] != $param['placing'])
        {
            if ($this->isExist('placing', $param['placing']))
            {
                return ['status' => 'failed', 'message' => 'Placing is exist, try another'];
            }
        }

        $this->db->where($condition);
        $this->db->update($this->main_table, $param);
        
        return ['status' => 'success', 'message' => 'Successfully updated.'];
    }

    /**
     * Is exist.
     * 
     * @return bool
     */
    public function isExist($field, $content)
    {
        $this->db->select('id');
        $this->db->from($this->main_table);
        $this->db->where($field, $content);
        $total = $this->db->get()->num_rows();
        
        if ($total > 0)
            return true;
        
        return false;
    }
}