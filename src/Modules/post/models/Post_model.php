<?php

/**
 * Post
 * 
 * Post business model.
 * 
 * @author Oriza
 */

use Symfony\Component\Yaml\Yaml;

class Post_model extends CI_Model
{
	protected $posts = 'mein_posts';
	protected $meta;
    protected $meta_prefix = 'mein_post_';
	protected $term_relationships = 'mein_term_relationships';
	protected $term_taxonomy = 'mein_term_taxonomy';
	protected $terms = 'mein_terms';
	protected $users = 'mein_users';
    protected $posttype = [];

	public function __construct()
	{
		parent::__construct();

        $posttype = Yaml::parse(setting_item('post.posttype_config'));
        $posttypeEntryConfig = [];
        foreach ($posttype as $type => $conf) {
            $posttypeEntryConfig[$type] = array_merge($conf, config_item('entries')[$conf['entry']] ?? []);
        }
        $this->posttype = $posttypeEntryConfig;
    }

    public function getPostType()
    {
        return $this->posttype;
    }
    
    public function getPosts($type = 'all', $result = 'total', $status = 'all', $limit = 10, $offset = 0, $orderby = 'created_at', $sort = 'desc', $resultType = 'stdClass', $user = 'all')
	{
		if ($result == 'total')
			$select = $this->posts.'.id';
		else
            $select = 'title, intro, featured_image, embed_video, video_duration, author, featured, slug,' . $this->posts . '.status as status, ';
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');

        if($meta_table = $this->posttype[$type]['table'] ?? ''){
            $select .= ", ".$meta_table.".*, ";
            $this->db->join($meta_table, $this->posts.'.id'.'='.$meta_table.'.post_id');
        }

        $select .= $this->posts . '.id as id,' . $this->posts . '.created_at as created_at,' . $this->posts . '.published_at as published_at';
        $this->db->select($select);
        
        if ($user != 'all') {
            $this->db->where($this->users.'.username', $user);
        }

        if ($type != 'all') {
            $this->db->where($this->posts.'.type', $type);
        }
        
        if ($status != 'all') {
            $this->db->where($this->posts.'.status', $status);
        } else {
            $this->db->where($this->posts.'.status !=', 'trash');
        }

		if ($result == 'total') {
			return $this->db->count_all_results();
        }

		$this->db->order_by($this->posts . '.'.$orderby, $sort);
		$this->db->limit($limit, $offset);
        
        if ($resultType == 'stdClass')
		    return $this->db->get()->result();
        else
            return $this->db->get()->result_array();
    }

    public function getEvents($filter = null, $result = 'total', $status = 'all', $limit = 10, $offset = 0, $orderby = 'created_at', $sort = 'desc', $resultType = 'stdClass', $user = 'all')
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('*,' . $this->posts . '.id as id,' . $this->posts . '.status as status,' . $this->posts . '.created_at as created_at');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');
        $this->db->join('mein_post_event', 'mein_post_event.post_id = ' . $this->posts . '.id', 'left');
        
        $this->db->where($this->posts.'.type', 'event');
        
        // By show.
        if ($filter == null) {
            $this->db->where('MONTH(event_tanggal)', date('m'));
            $this->db->where('YEAR(event_tanggal)', date('Y'));
        } else if ($filter == 'upcoming') {
            $this->db->where('event_tanggal >=', date('Y-m-d H:i:s'));
        } else if ($filter == 'expired') {
            $this->db->where('event_tanggal <=', date('Y-m-d H:i:s'));
        } else {
            // Return all.
        }

        if ($user != 'all') {
            $this->db->where($this->users.'.username', $user);
        }

        if ($status != 'all') {
            $this->db->where($this->posts.'.status', $status);
        }

		if ($result == 'total') {
			return $this->db->count_all_results();
        }

		$this->db->order_by('mein_post_event.'. $orderby, $sort);
		$this->db->limit($limit, $offset);
        
        if ($resultType == 'stdClass')
		    return $this->db->get()->result();
        else
            return $this->db->get()->result_array();
    }

    public function getWebinars($filter = null, $result = 'total', $status = 'all', $limit = 10, $offset = 0, $orderby = 'created_at', $sort = 'desc', $resultType = 'stdClass', $user = 'all')
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('*,' . $this->posts . '.id as id,' . $this->posts . '.status as status,' . $this->posts . '.created_at as created_at');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');
        $this->db->join('mein_post_webinar', 'mein_post_webinar.post_id = ' . $this->posts . '.id', 'left');
        
        $this->db->where($this->posts.'.type', 'webinar');
        
        // By show.
        if ($filter == 'upcoming') {
            $this->db->where('scheduled_at >=', date('Y-m-d H:i:s'));
        } else if ($filter == 'reply') {
            $this->db->where('scheduled_at <=', date('Y-m-d H:i:s'));
        } else {
            // Return all.
        }

        if ($user != 'all') {
            $this->db->where($this->users.'.username', $user);
        }

        if ($status != 'all') {
            $this->db->where($this->posts.'.status', $status);
        }

		if ($result == 'total') {
			return $this->db->count_all_results();
        }
        
		$this->db->order_by($this->posts . '.created_at', 'desc');
        
        $this->db->limit($limit, $offset);
        
        if ($resultType == 'stdClass')
		    return $this->db->get()->result();
        else
            return $this->db->get()->result_array();
    }

    public function getFeaturedPosts($type = 'all', $result = 'total', $status = 'all', $limit = 10, $offset = 0, $orderby = 'created_at', $sort = 'desc', $resultType = 'stdClass', $user = 'all')
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('title, slug,' . $this->posts . '.id as id,' . $this->posts . '.status as status,' . $this->posts . '.created_at as created_at');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');
        $this->db->join($this->meta, $this->meta . '.post_id = ' . $this->posts . '.id', 'left');
        
        $this->db->where($this->posts.'.featured !=', null);
        
        if ($user != 'all') {
            $this->db->where($this->users.'.username', $user);
        }

        if ($type != 'all') {
            $this->db->where($this->posts.'.type', $type);
        }
        
        if ($status != 'all') {
            $this->db->where($this->posts.'.status', $status);
        }

		if ($result == 'total') {
			return $this->db->count_all_results();
        }

		$this->db->order_by($this->posts . '.'.$orderby, $sort);
		$this->db->limit($limit, $offset);
        
        if ($resultType == 'stdClass')
		    return $this->db->get()->result();
        else
            return $this->db->get()->result_array();
    }

    public function getPostsByCategory($category_slug, $type = 'post', $result = 'total', $status = 'publish', $limit = 10, $offset = 0, $result_type = 'object', $category_exclude = [])
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('title, intro, featured_image, embed_video, author, featured, '. $this->posts.'.slug,' . $this->posts . '.id as id,' . $this->posts . '.status as status,' . $this->posts . '.created_at as created_at,' . $this->posts . '.published_at as published_at');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id =' . $this->posts.'.author');
        $this->db->join($this->term_relationships, $this->term_relationships . '.object_id =' . $this->posts . '.ID');
        $this->db->join($this->term_taxonomy, $this->term_taxonomy . '.term_taxonomy_id =' . $this->term_relationships . '.term_taxonomy_id');
        $this->db->join($this->terms, $this->terms . '.term_id =' . $this->term_taxonomy . '.term_id');

		$this->db->where($this->posts.'.status', $status);
		$this->db->where($this->posts.'.type', $type);

        if($category_slug != 'all')
    		$this->db->where($this->terms.'.slug', $category_slug);
        else if ($category_exclude)
            $this->db->where_not_in($this->terms.'.slug', $category_exclude);

		$this->db->where($this->term_taxonomy.'.taxonomy', $type . '_category');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();
		
		$this->db->order_by($this->posts . '.created_at', 'desc');
		$this->db->limit($limit, $offset);
		
		return $this->db->get()->result($result_type);
    }

    public function getPostsByTag($tag_slug, $type = 'post', $result = 'total', $status = 'publish', $limit = 10, $order = 0)
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
			$this->db->select('*,' . $this->posts.'.id as id,' . $this->posts . '.content as contents,' . $this->posts . '.status as status,' . $this->posts . '.slug as slug');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id =' . $this->posts.'.author');
        $this->db->join($this->term_relationships, $this->term_relationships . '.object_id =' . $this->posts . '.ID');
        $this->db->join($this->term_taxonomy, $this->term_taxonomy . '.term_taxonomy_id =' . $this->term_relationships . '.term_taxonomy_id');
        $this->db->join($this->terms, $this->terms . '.term_id =' . $this->term_taxonomy . '.term_id');

		$this->db->where($this->posts.'.status', $status);
		$this->db->where($this->posts.'.type', $type);
		$this->db->where($this->terms.'.slug', $tag_slug);
		$this->db->where($this->term_taxonomy.'.taxonomy', 'tag');
		
		if ($result == 'total')
			return $this->db->get()->num_rows();
		
		$this->db->order_by($this->posts . '.created_at', 'desc');
		$this->db->limit($limit, $order);
		
		return $this->db->get()->result();
    }
    
    public function searchPosts($keyword = null, $type = null, $result = null, $status = null, $limit = null, $order = null)
	{
		if ($result == 'total')
			$this->db->select($this->posts.'.id');
		else
            $this->db->select('*,' . $this->posts . '.id as id,' . $this->posts . '.content as contents,' . $this->posts . '.status as status');
		
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author');
        $this->db->like($this->posts.'.title', $keyword);
        
        if ($status != 'all')
            $this->db->where($this->posts.'.status', $status);
        else
            $this->db->where_in($this->posts.'.status', ['publish', 'draft']);
        
        if ($type != 'all')
            $this->db->where($this->posts . '.type', $type);
        
		if ($result == 'total')
			return $this->db->get()->num_rows();
		
		$this->db->order_by($this->posts . '.created_at', 'desc');
        
        if ($limit != null)
            $this->db->limit($limit, $order);
        
		return $this->db->get()->result();
    }
    
    public function getFeaturedImage($post_id, $size = '300x300')
	{
		$this->db->select('featured_image');
		$this->db->from($this->posts);
		$this->db->where('id', $post_id);
        
		$result = $this->db->get()->row();
        
        if (!empty($result->featured_image))
        {
            // Dari CDN CDPL
            if (preg_match("/cdn-cdpl.com/i", $result->featured_image))
            {
                $size = explode('x', $size);

                return str_replace('https://static.cdn-cdpl.com/source/', 'https://static.cdn-cdpl.com/'. $size[0] . 'x' . $size[1] . '/', $result->featured_image);
            }
            else if (preg_match("/www.codepolitan.com/i", $result->featured_image)) 
            {
                $size = explode('x', $size);
                
                // Source Laravel Img.
                $cover = str_replace('https://www.codepolitan.com/uploads/image/', 'https://static.cdn-cdpl.com/', $result->featured_image);

                $exploded = explode('.jpg', $cover);
                $ext = 'jpg';

                if (!isset($exploded[1]))
                {
                    $exploded = explode('.png', $cover);
                    $ext = 'png';
                }

                if (!isset($exploded[1]))
                {
                    $exploded = explode('.gif', $cover);
                    $ext = 'gif';
                }

                if (!isset($exploded[1]))
                    return false;

                $final_cover = $exploded[0] . '-image('. $size[0] .'x'. $size[1] . '-crop).' . $ext;

                return $final_cover;
            }
            else
            {
                // Dari Local.
                if ($size != null)
                {
                    $final_cover = str_replace('uploads/sources/', 'uploads/' . $size . '/', $result->featured_image);
                    
                    return $final_cover;
                }
                else
                {
                    return $result->featured_image;
                }
            }
        }
        else
        {
            $this->db->select('*');
            $this->db->from('cp_postmeta');
            $this->db->where('post_id', $post_id);
            $this->db->where('meta_key', '_thumbnail_id');
            
            $meta = $this->db->get()->row();
            
            if (!empty($meta))
            {
                $this->db->select('*');
                $this->db->from('cp_postmeta');
                $this->db->where('post_id', $meta->meta_value);
                $this->db->where('meta_key', '_wp_attached_file');

                $meta_file = $this->db->get()->row();

                $cover = 'https://static.cdn-cdpl.com/wp-images/' . $meta_file->meta_value;

                $exploded = explode('.jpg', $cover);
                $ext = 'jpg';

                if (!isset($exploded[1]))
                {
                    $exploded = explode('.png', $cover);
                    $ext = 'png';
                }

                if (!isset($exploded[1]))
                {
                    $exploded = explode('.gif', $cover);
                    $ext = 'gif';
                }

                if (!isset($exploded[1]))
                    return false;
                
                // Dari wordpress siapa tahu ..
                $sizeArr = explode('x', $size);

                $final_cover = $exploded[0] . '-image('. $sizeArr[0] .'x'. $sizeArr[1] . '-crop).' . $ext;

                return $final_cover;
            }
            else
            {
                return 'https://via.placeholder.com/' . $size;
            }
        }
	}

	public function getRandomPosts($limit)
	{
		$sql = "SELECT ID FROM $this->posts, (SELECT ID AS sid FROM $this->posts ORDER BY RAND() LIMIT $limit) tmp WHERE $this->posts.ID = tmp.sid ORDER BY $this->posts.ID";
		$query = $this->db->query($sql);
		
		return $query->result();
	}

	public function getRelatedPosts($id, $keyword, $type, $limit)
	{
        $this->meta = $this->meta_prefix.$type;
        $meta_exists = $this->db->table_exists($this->meta);

        $select = 'title, ' . $this->posts . '.created_at, ' . $this->posts . '.id as id,' . $this->posts . '.slug as slug';

        if($meta_exists) $select = $this->meta.'.*, '.$select;

		$this->db->select($select);
		$this->db->from($this->posts);

        if($meta_exists)
    		$this->db->join($this->meta, $this->meta . '.post_id = ' . $this->posts . '.id', 'left');

        $this->db->join($this->term_relationships, $this->term_relationships.'.object_id'. '=' .$this->posts.'.id', 'left');
		$this->db->join($this->term_taxonomy , $this->term_taxonomy.'.term_taxonomy_id'. '=' . $this->term_relationships.'.term_taxonomy_id', 'left');
		$this->db->join($this->terms, $this->terms .'.term_id'. '=' .$this->term_taxonomy. '.term_id', 'left');

	    // If tags is not empty return by related tags
		if (!empty($keyword))
		{
            $this->db->where_in($this->terms.'.name', $keyword);
        }

		$this->db->where($this->term_taxonomy.'.taxonomy', 'tag');
		$this->db->where($this->posts.'.status', 'publish');
		// $this->db->where($this->posts.'.type', $type);
		$this->db->where($this->posts.'.id !=', $id);
        
		// Limitation
		if (!empty($limit))
		{
			$this->db->limit($limit);
		}
        
		$this->db->order_by($this->posts.'.created_at','desc');

		return $this->db->get()->result_array();
	}
    
	public function getPost($status, $by_field, $by_field_value, $parse = true)
	{
		$this->db->select($this->posts .'.*, ' . $this->users.'.username, ' . $this->users.'.name, ' . $this->posts . '.id as id,' . $this->posts . '.status as status');
		$this->db->from($this->posts);
		$this->db->join($this->users, $this->users.'.id'.'='.$this->posts.'.author', 'left');
        
		// By any field. Can be by slug or id.
		$this->db->where($this->posts.'.'.$by_field, $by_field_value);
		
		if ((isset($status)) && (!empty($status)))
		{
			$this->db->where($this->posts.'.status', $status);
		}
        
        $data = $this->db->get()->row_array();

        return $data;
	}

    public function getMeta($post_type, $post_id)
    {
        $meta_table = $this->posttype[$post_type]['table'] ?? '';
        if(!$meta_table) return [];

        return $this->db->where('post_id', $post_id)->get($meta_table)->row_array();
    }

    /** 
     * Get Post Field.
     * 
     * Get email/author/status/title etc by field.
     * 
     * @param string $field_to_get
     * @param string $by_field
     * @param string $by_field_value
     * @return mixed
     */
    public function getPostField($field_to_get, $by_field, $by_field_value)
	{
		$this->db->select($field_to_get . ' as field_to_get');
		$this->db->from($this->posts);
		$this->db->where($by_field, $by_field_value);

		$result = $this->db->get()->row();

		if (!empty($result))
		    return $result->field_to_get;
        
        return null;
	}

	public function getTypes()
	{
		$this->db->select('type');
		$this->db->from($this->posts);
		$this->db->group_by('type');

		return $this->db->get()->result();
	}
    
    /** 
     * Insert Post
     * 
     * @return array 
     */
	public function insert($param)
	{
        // Dependency
        $this->load->library('form_validation');

        // Do validation.
        $this->form_validation->set_data($param);
        $this->form_validation->set_rules('title', 'Title', 'trim|required')
                              // ->set_rules('status', 'Status', 'trim|required')
                              ->set_rules('post_type', 'Post Type', 'trim|required')
                              ->set_rules('content', 'Content', 'required')
                              ->set_rules('author', 'The author', 'trim|required')
                              ->set_rules('slug', 'Slug', 'trim|required');

        if ($this->form_validation->run() == false)
		    return ['status' => 'failed', 'message' => validation_errors()];
        
        if ($this->isExist('slug', $param['slug']))
		    return ['status' => 'failed', 'message' => $this->lang->line('mein_error_slug')];

		// Insert post
		$this->db->insert($this->posts, [
			'title' => $param['title'],
			'content' => $param['content'],
			'intro' => $param['intro'],
            'content_type' => $param['content_type'],
			'featured_image' => $param['featured_image'],
            'embed_video' => $param['embed_video'],
            'video_duration' => $param['video_duration'],
			'author' => $param['author'],
			'status' => 'draft',
			'slug' => url_title($param['slug'], '-', true),
			'type' => $param['post_type'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

		$inserted_id = $this->db->insert_id();
        $param = array_merge($param, ['id' => $inserted_id]);
        
        // Connect category.
        if ($param['category_id'] ?? '') {
            $this->Taxonomy_model->connect_category($param['category_id'], $param['post_type'], $inserted_id);
        }

        // Connect Tag.
        if ($param['tags'] ?? '') {
            $this->Taxonomy_model->connect_tag($param['tags'], $inserted_id);
        }

        $this->event->trigger('Post_model.insert', $param);

        return ['status' => 'success', 'message' => 'Successfully added', 'inserted_id' => $inserted_id];
	}

	public function draft($id)
	{
		return $this->db->update($this->posts, ['status' => 'draft'], ['id' => $id]);
	}

	public function publish($id)
	{
		return $this->db->update($this->posts, [
            'status' => 'publish',
            'published_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }
    
    public function review($id)
	{
		return $this->db->update($this->posts, [
            'status' => 'review'
        ], ['id' => $id]);
	}

	public function trash($id)
	{
		return $this->db->update($this->posts, ['status' => 'trash'], ['id' => $id]);
	}

	public function restore($id)
	{
		return $this->db->update($this->posts, ['status' => 'draft'], ['id' => $id]);
	}

	public function increaseTotalSeen($post_id)
	{
		$sql = "UPDATE $this->posts SET total_seen = total_seen + 1 WHERE ID = '$post_id'";
		$this->db->query($sql);
        
        return true;
	}

    /** 
     * Update Post
     * 
     * @return array 
     */
	public function update($post_id, $param)
	{
        // Slug check.
        $previous_slug = $this->getPostField('slug', 'id', $post_id);

        if ($param['slug'] != $previous_slug)
        {
            if ($this->isExist('slug', $param['slug']))
            {
                return ['status' => 'failed', 'message' => 'Slug is used by other article.'];
            }
		}
        
        // Connect category.
        if ($param['category_id'] ?? '') {
            $this->Taxonomy_model->connect_category($param['category_id'], $param['post_type'], $post_id);
        }

        // Connect Tag.
        if ($param['tags'] ?? '') {
            $this->Taxonomy_model->connect_tag($param['tags'], $post_id);
        }

        // Do validate
        $this->load->library('form_validation');
        $this->form_validation->set_data($param);
        $this->form_validation->set_rules('title', 'Title', 'trim|required')
                              ->set_rules('content', 'Content', 'required')
                              ->set_rules('author', 'The author', 'trim|required')
                              ->set_rules('slug', 'Slug', 'trim|required');

        if ($this->form_validation->run() == false)
		    return ['status' => 'failed', 'message' => validation_errors()];
        
        if (isset($param['featured'])) {
            if ($param['featured'] == 'Yes')
                $param['featured'] = date('Y-m-d H:i:s');
            else
                $param['featured'] = null;    
        } else {
            $param['featured'] = null;
        }
        
        // Prepare param ..
        $param_ = [
			'slug' => url_title($param['slug'], '-', true),
			'title' => $param['title'],
            'featured_image' => $param['featured_image'],
            'embed_video' => $param['embed_video'],
            'video_duration' => $param['video_duration'],
			'content' => $param['content'],
			'intro' => $param['intro'],
            'content_type' => $param['content_type'],
			'featured' => $param['featured'],
            'published_at' => $param['published_at'] ?? ''
        ];

        if (isset($param['template']))
            $param_['template'] = $param['template'];
        
        if (isset($param['status']))
            $param_['status'] = $param['status'];
        
		$this->db->update($this->posts, $param_, ['id' => $post_id]);
        
		return ['status' => 'success', 'message' => 'Successfully updated.'];
    }
    
    /**
     * Update Meta
     * 
     * @return array
     */
    public function updateMeta($post_type, $post_id, $sets)
    {
        if (empty($sets))
            return true;

        $meta_table = $this->posttype[$post_type]['table'] ?? '';
        if(!$meta_table)
            throw new Exception('Meta table for post type $posttype not defined');

        // Is record exist?
        $this->db->select('id');
        $this->db->from($meta_table);
        $this->db->where('post_id', $post_id);
        $record = $this->db->get()->row();
        
        if (!empty($record))
        {
            $this->db->where('post_id', $post_id);
            $this->db->update($meta_table, $sets);
            
            return true;
        }

        $sets['post_id'] = $post_id;
        $this->db->insert($meta_table, $sets);
        
        return true;
    }

	public function delete($id)
	{
		return $this->db->update($this->posts, ['updated_at' => date('Y-m-d H:i:s'), 'status' => 'trash'], ['id' => $id]);
    }

	public function clear()
	{
		return $this->db->delete($this->posts, ['status' => 'preview']);
    }
    
	public function isExist($field, $value)
	{
		$this->db->select('id');
		$this->db->from($this->posts);
		$this->db->where($field, $value);
        
		$result = $this->db->get()->num_rows();

		if ($result > 0)
			return true;
        
        return false;
    }
}
