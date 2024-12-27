<?php

/**
 * Search
 * 
 * Search business model.
 * 
 * @author Oriza
 */

class Search_model extends CI_Model
{
	protected $posts = 'mein_posts';
	protected $meta = 'mein_post_meta';
	protected $term_relationships = 'mein_term_relationships';
	protected $term_taxonomy = 'mein_term_taxonomy';
	protected $terms = 'mein_terms';
	protected $users = 'mein_users';
    
	public function __construct()
	{
		parent::__construct();
    }
    
    public function search($keyword = null, $result = 'total', $limit = 10, $offset = 0)
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('*,' . $this->posts . '.id as id,' . $this->posts . '.created_at as created_at,' . $this->posts . '.status as status');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');
        
        $this->db->where($this->posts.'.status', 'publish');
        $this->db->like($this->posts.'.title', $keyword);
        
		if ($result == 'total') {
			return $this->db->count_all_results();
        }

		$this->db->order_by($this->posts . '.created_at', 'desc');
		$this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }
}