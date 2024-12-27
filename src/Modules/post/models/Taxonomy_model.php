<?php

/**
 * Taxonomy Library
 * 
 * This is taxonomy business model for Mein CMS.
 * 
 * @author Oriza
 */

class Taxonomy_model extends CI_Model
{
	protected $posts = 'mein_posts';
	protected $terms = 'mein_terms';
	protected $term_relationships = 'mein_term_relationships';
	protected $term_taxonomy = 'mein_term_taxonomy';
    
	public function __construct()
	{
		parent::__construct();
    }
    
    public function get_all($type, $limit = null, $limit_order = null)
	{
		$this->db->select($this->terms.'.slug,'.$this->terms.'.term_id,'.$this->terms.'.name');
		$this->db->from($this->term_taxonomy);
		$this->db->join($this->terms, $this->terms.'.term_id'.'='.$this->term_taxonomy.'.term_id');
		$this->db->where('taxonomy', $type);
		$this->db->where('parent', '0');

		if ($limit != null)
		{
			$this->db->limit($limit, $limit_order);
		}

		$this->db->order_by('term_id','desc');
		return $this->db->get()->result();
    }

    public function get_total($type)
	{
		$sql = "SELECT b.term_id FROM $this->term_taxonomy AS a INNER JOIN $this->terms AS b ON a.term_id = b.term_id WHERE a.taxonomy = '$type' AND a.parent = '0'";

		$query = $this->db->query($sql);
        
        return $query->num_rows();
	}

    public function search($type, $keyword = null, $limit = null, $limit_order = null)
	{
		$this->db->select($this->terms.'.slug,'.$this->terms.'.term_id,'.$this->terms.'.name');
		$this->db->from($this->term_taxonomy);
		$this->db->join($this->terms, $this->terms.'.term_id'.'='.$this->term_taxonomy.'.term_id');
        $this->db->like($this->terms . '.name', $keyword ?? '');
		$this->db->where('taxonomy', $type);
		$this->db->where('parent', '0');

		if ($limit != null)
		{
			$this->db->limit($limit, $limit_order);
		}

		$this->db->order_by('term_id','desc');
		return $this->db->get()->result();
    }

    public function search_total($type, $keyword)
	{
		$sql = "SELECT b.term_id FROM $this->term_taxonomy AS a INNER JOIN $this->terms AS b ON a.term_id = b.term_id WHERE b.name LIKE '%$keyword%' AND a.taxonomy = '$type' AND a.parent = '0'";

		$query = $this->db->query($sql);
        
        return $query->num_rows();
	}
    
    public function get_detail($term_id)
	{
		$this->db->select('*');
		$this->db->from($this->terms);
		$this->db->where('term_id', $term_id);

		return $this->db->get()->row();
	}

    /**
     * Insert new category / tag
     * 
     * @return array 
     */
	public function insert($type, $param)
	{
        if (empty($param['name']) || empty($param['slug'])) 
            return ['status' => 'failed', 'message' => 'Name and slug is required.'];
        
        if ($this->is_term_exist('slug', $param['slug']))
            return ['status' => 'failed', 'message' => 'Slug is exist before.'];
        
        // Insert master term
        $term = $this->db->insert($this->terms, [
            'name' => $param['name'], 
            'slug' => $param['slug']
        ]);

        if ($term)
        {
            // Insert taxonomy
			$this->db->select('term_id');
			$this->db->from($this->terms);
			$this->db->where('slug', $param['slug']);
			$result = $this->db->get()->row();
            
			if (!empty($result))
			{
				$this->db->insert($this->term_taxonomy, ['term_id' => $result->term_id, 'taxonomy' => $type]);
            }
		}
        
        return ['status' => 'success', 'message' => 'Successfully inserted', 'term_id' => $result->term_id];
	}

	public function insert_relation($term_taxonomy_id, $object_id)
	{
		$this->db->insert($this->term_relationships, ['object_id' => $object_id, 'term_taxonomy_id' => $term_taxonomy_id]);
        
        return true;
    }
    
    /** 
     * Connect category to object. 
     * 
     * @return bool
     */
    public function connect_category($category_id, $post_type, $object_id)
	{
        // Get taxonomy id
		$term_taxonomy_id = $this->Taxonomy_model->get_term_taxonomy_id($category_id, $post_type, 'category');

		if (empty($term_taxonomy_id))
            return false;
        
        // Reset relation.
        $this->Taxonomy_model->delete_relation('category', $object_id);

		// Do relation.
		if (!$this->Taxonomy_model->is_relation_exist($term_taxonomy_id, $object_id))
		    $this->Taxonomy_model->insert_relation($term_taxonomy_id, $object_id);
        
        return true;
    }

    /** 
     * Connect tag to object. 
     * 
     * @return bool
     */
    public function connect_tag($tags, $object_id)
	{
        // Convert tag to array
        $tags = explode(',', $tags);

        // Reset/delete all post tags relation
        $this->delete_relation('tag', $object_id);
        
        // Insert tag to master.
		foreach ($tags as $tag)
		{
            $name = trim($tag);
            $slug = slugify($name);

            if (!$this->Taxonomy_model->is_term_exist('slug', $slug))
			    $this->Taxonomy_model->insert('tag', ['name' => $tag, 'slug' => $slug]);
        }
        
		// Connect tags to relationships.
		foreach ($tags as $tag)
		{
            $name = trim($tag);
            $slug = slugify($name);

            $term_taxonomy = $this->Taxonomy_model->get_field_value($this->term_taxonomy . '.term_taxonomy_id', $this->terms . '.slug', $slug);

            if (!empty($term_taxonomy))
            {
                // Connect to object.
                if (!$this->Taxonomy_model->is_relation_exist($term_taxonomy->term_taxonomy_id, $object_id))
			        $this->Taxonomy_model->insert_relation($term_taxonomy->term_taxonomy_id, $object_id);
            }   
        }
        
        return true;
    }
	
	public function get_category($post_id)
	{
		$sql = "SELECT d.term_id, d.slug, d.name FROM $this->posts AS a LEFT JOIN ($this->term_relationships AS b, $this->term_taxonomy AS c, $this->terms AS d) ON (a.id = b.object_id AND b.term_taxonomy_id = c.term_taxonomy_id AND c.term_id = d.term_id) WHERE a.id = '$post_id' AND c.taxonomy LIKE '%category%' AND c.parent = '0' LIMIT 1";
		$result = $this->db->query($sql)->row();

		if ($result != null)
            return $result;
		
		return null;
	}

	public function get_tags($post_id, $result = 'string')
	{
        $sql = "SELECT d.name, d.slug FROM $this->posts AS a LEFT JOIN ($this->term_relationships AS b, $this->term_taxonomy AS c, $this->terms AS d) ON (a.id = b.object_id AND b.term_taxonomy_id = c.term_taxonomy_id AND c.term_id = d.term_id) WHERE a.id = '$post_id' AND c.taxonomy = 'tag'";
		$query = $this->db->query($sql);
        
        $results = $query->result_array();
        
        if ($result == 'string')
        {
            $tags = '';

            foreach ($results as $r)
			    $tags .= $r['name'] . ', ';
            
            return rtrim($tags, ', ');
        }

        return $results;
    }
    
	public function get_term_taxonomy_id($term_id, $post_type, $taxonomy = 'category')
	{
		$this->db->select('term_taxonomy_id');
		$this->db->from($this->term_taxonomy);
		$this->db->where('term_id', $term_id);
		$this->db->where('taxonomy', $post_type . '_' . $taxonomy);
        
        $result = $this->db->get()->row();

		if (!empty($result))
		    return $result->term_taxonomy_id;
        
        return null;
	}

	public function get_slug_name_by_id($term_id)
	{
		$this->db->select('slug');
		$this->db->from($this->terms);
		$this->db->where('term_id', $term_id);
		$result = $this->db->get()->row();

		if (!empty($result))
		    return $result->slug;
        
        return null;
	}

	public function get_field_value($field_name, $by_field, $field_value)
	{
		$this->db->select($field_name);
		$this->db->from($this->terms);
		$this->db->join($this->term_taxonomy, $this->terms.'.term_id'.'='.$this->term_taxonomy.'.term_id');
		$this->db->where($by_field, $field_value);
        
        return $this->db->get()->row();
	}

	public function update($term_id, $param)
	{
        if (empty($param['name']) || empty($param['slug'])) 
            return ['status' => 'failed', 'message' => 'Name and slug is required.'];

		$this->db->where('term_id', $term_id);
        $this->db->update($this->terms, $param);
        
        return ['status' => 'success', 'message' => 'Successfully updated.'];
    }

	public function is_term_exist($by_field, $field_value)
	{
		$this->db->select('term_id');
		$this->db->from($this->terms);
		$this->db->where($by_field, $field_value);
		$result = $this->db->get()->num_rows();

		if (!empty($result))
		    return true;
        
        return false;
	}

	public function is_relation_exist($term_taxonomy_id, $object_id)
	{
		$this->db->select('object_id');
		$this->db->from($this->term_relationships);
		$this->db->where('term_taxonomy_id', $term_taxonomy_id);
		$this->db->where('object_id', $object_id);
		$result = $this->db->get()->result();

		if (!empty($result))
		    return true;
        
        return false;
	}

	public function delete_relation($type, $object_id)
	{
		if ($type == 'all')
		{
			$sql = "DELETE FROM $this->term_relationships WHERE object_id = '$object_id'";
		}
		else if ($type == 'category')
		{
			$sql = "DELETE s.* FROM $this->term_relationships s INNER JOIN $this->term_taxonomy n ON s.term_taxonomy_id = n.term_taxonomy_id WHERE s.object_id = '$object_id' AND n.taxonomy LIKE '%category%' ";
		}
		else if ($type == 'tag')
		{
			$sql = "DELETE s.* FROM $this->term_relationships s INNER JOIN $this->term_taxonomy n ON s.term_taxonomy_id = n.term_taxonomy_id WHERE s.object_id = '$object_id' AND n.taxonomy = 'tag' ";
		}

		$query = $this->db->query($sql);
        
        return true;
	}

	public function delete($term_id)
	{
        // Find it's taxonomy
		$this->db->select('term_taxonomy_id, taxonomy');
		$this->db->from($this->term_taxonomy);
		$this->db->where('term_id', $term_id);
		$result = $this->db->get()->row();
        
        $term_taxonomy_id = $result->term_taxonomy_id;
		$term_type =$result->taxonomy;

        // It's a tags.
		if ($term_type == 'tag')
		{
            // it's tag, delete all the term relationship value that related to the term taxonomy id
			$this->db->delete($this->term_relationships, ['term_taxonomy_id' => $term_taxonomy_id]);

			// Delete term
			$this->db->delete($this->terms, ['term_id' => $term_id]);
			$this->db->delete($this->term_taxonomy, ['term_id' => $term_id]);
        }
        else
        {
			// It's category, change all the term relationship value that related to the term taxonomy id become uncategories
			$this->db->update($this->term_relationships, ['term_taxonomy_id' => 1], ['term_taxonomy_id' => $term_taxonomy_id]);

			// Okey we have done a good job, then say bye bye to the term
			$this->db->delete($this->terms, ['term_id' => $term_id]);
			$this->db->delete($this->term_taxonomy, ['term_id' => $term_id]);
        }
		
        return true;
	}
}