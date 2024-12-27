<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *	Post Shortcode
 *	
 *  Theme api for Post feature
 */
class PostShortcode extends Shortcode {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('post/Post_model');
    }
    
    /**
	 * Get post
	 */
	public function getPost()
	{        
        $this->load->model('post/Taxonomy_model');
        
		$slug = $this->getAttribute('slug', null);
		$status = $this->getAttribute('status', 'publish');
		
        $post = $this->Post_model->getPost($status, 'slug', $slug, 'array');
        
        return $this->output($post);
    }

    /**
	 * Get all posts
	 */
	public function getPosts()
	{       
        $this->load->model('post/Taxonomy_model');
        
		// Retrieve extension attributes
		$user = $this->getAttribute('user', 'all');
		$type = $this->getAttribute('type', 'all');
		$status = $this->getAttribute('status', 'all');
		$orderby = $this->getAttribute('orderby', 'published_at');
		$sort = $this->getAttribute('sort', 'desc');
		
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 8);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		
		// Menghindari error jika value segment bukan halaman.
		if (!is_numeric($pagenum)) {
			redirect('articles');
		}

		$pagenum = abs($pagenum);

		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getPosts($type, 'loop', $status, $perpage, $offset, $orderby, $sort, 'array', $user);
        
        // Inject category ..
        foreach($posts as $key => $value)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($value['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = site_url($this->getAttribute('uri'));
		$config['total_rows'] = $this->Post_model->getPosts($type, 'total', $status, null, null, null, null, null, $user);
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();

		return $this->output($data);
	}
	
	/**
	 * Get Posts By User
	 */
	public function getPostsByUser()
	{       
        $this->load->model('post/Taxonomy_model');
        
		$type = $this->getAttribute('type', 'all');
		$status = $this->getAttribute('status', 'all');
		$posts = $this->Post_model->getPosts($type, 'loop', $status, 10, 0, 'id', 'desc', 'array', $this->uri->segment(2));
		
		return ['posts' => $posts];
    }

    /**
	 * Get related posts
	 */
	public function getRelatedPosts()
	{       
        $this->load->model('post/Taxonomy_model');
        
		// Retrieve extension attributes
		$type = $this->getAttribute('type', 'post');
		$keyword = $this->getAttribute('keyword', 'all');
		$limit = $this->getAttribute('limit', '4');
		$id = $this->getAttribute('id', null);
        
        // Pecah jadi array
        $keyword = explode(', ', $keyword);
        
        $posts = $this->Post_model->getRelatedPosts($id, $keyword, $type, $limit);
        
        // Inject tags ..
        foreach($posts as $key => $value)
        {
            $posts[$key]['tags'] = $this->Taxonomy_model->get_tags($value['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;
        
		return $this->output($data);
    }

    /**
	 * Get upcoming events
	 */
	public function getEvents()
	{       
        $this->load->model('post/Taxonomy_model');
        
		// Retrieve extension attributes
		$filter = $this->uri->segment(2);
		$user = $this->getAttribute('user', 'all');
		$status = $this->getAttribute('status', 'all');
		$orderby = $this->getAttribute('orderby', 'created_at');
		$sort = $this->getAttribute('sort', 'desc');
        
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 8);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getEvents($filter, 'loop', $status, $perpage, $offset, $orderby, $sort, 'array', $user);
        
        // Inject category ..
        foreach($posts as $key => $value)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($value['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = site_url($this->getAttribute('uri') . '/' . $this->uri->segment(2));
		$config['total_rows'] = $this->Post_model->getEvents($filter, 'total', $status, null, null, null, null, null, $user);
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
        
		return $this->output($data);
	}
	
	/**
	 * Get upcoming webinars
	 */
	public function getWebinars()
	{       
        $this->load->model('post/Taxonomy_model');
        
		// Retrieve extension attributes
		$filter = $this->uri->segment(2);
		$user = $this->getAttribute('user', 'all');
		$status = $this->getAttribute('status', 'all');
		$orderby = $this->getAttribute('orderby', 'published_at');
		$sort = $this->getAttribute('sort', 'desc');
        
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 8);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getWebinars($filter, 'loop', $status, $perpage, $offset, $orderby, $sort, 'array', $user);
        
        // Inject category ..
        foreach($posts as $key => $value)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($value['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = site_url($this->getAttribute('uri') . '/' . $this->uri->segment(2));
		$config['total_rows'] = $this->Post_model->getWebinars($filter, 'total', $status, null, null, null, null, null, $user);
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
        
		return $this->output($data);
    }
    
    /**
	 * Get featured posts
	 */
	public function getFeaturedPosts()
	{
        $this->load->model('post/Taxonomy_model');
        
		// Retrieve extension attributes
		$user = $this->getAttribute('user', 'all');
		$type = $this->getAttribute('type', 'all');
		$status = $this->getAttribute('status', 'all');
		$orderby = $this->getAttribute('orderby', 'published_at');
		$sort = $this->getAttribute('sort', 'desc');
		
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 8);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getFeaturedPosts($type, 'loop', $status, $perpage, $offset, $orderby, $sort, 'array', $user);
        
        // Inject category ..
        foreach($posts as $key => $value)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($value['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = site_url($this->getAttribute('uri'));
		$config['total_rows'] = $this->Post_model->getFeaturedPosts($type, 'total', $status, null, null, null, null, null, $user);
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();

		return $this->output($data);
	}
    
    /**
	 * Get featured image.
	 */
    public function getFeaturedImage()
	{
        $id = $this->getAttribute('id');
		$size = $this->getAttribute('size', '270x135');
        
        return $this->Post_model->getFeaturedImage($id, $size);
    }
    
    /**
	 * Get all posts by tag
	 */
	public function getPostsByTag()
	{        
        $tag = $this->getAttribute('tag');
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 10);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		
		// Menghindari error jika value segment bukan halaman.
		if (!is_numeric($pagenum)) {
			redirect('tag/' . $tag);
		}

		$pagenum = abs($pagenum);
		
		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$data['posts'] = $this->Post_model->getPostsByTag($tag, 'post', 'list', 'publish', $perpage, $offset);
        
		// Generate pagination links
		$config['base_url'] = site_url('tag/' . $this->getAttribute('uri'));
		$config['total_rows'] = $this->Post_model->getPostsByTag($tag, 'post', 'total', 'publish');
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
        
		return $this->output($data);
    }

    /**
	 * Get all posts by category
	 */
	public function getPostsByCategory()
	{        
        $category = $this->getAttribute('category');
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 10);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		
		// Menghindari error jika value segment bukan halaman.
		if (!is_numeric($pagenum)) {
			redirect(site_url('category/' . $this->getAttribute('uri')));
		}
		
		$pagenum = abs($pagenum);

		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$data['posts'] = $this->Post_model->getPostsByCategory($category, 'post', 'list', 'publish', $perpage, $offset);

		// Generate pagination links
		$config['base_url'] = site_url('category/' . $this->getAttribute('uri'));
		$config['total_rows'] = $this->Post_model->getPostsByCategory($category, 'post', 'total', 'publish');
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
        
		return $this->output($data);
	}
	
	/**
	 * Get all posts by author
	 */
	public function getPostsByAuthor()
	{        
        $author = $this->getAttribute('author');
		$page_segment = $this->getAttribute('page_segment');
		$perpage = $this->getAttribute('perpage', 10);
		$pagenum = $this->uri->segment($page_segment) ? $this->uri->segment($page_segment) : 1;
		
		// Menghindari error jika value segment bukan halaman.
		if (!is_numeric($pagenum)) {
			redirect(site_url('articles'));
		}
		
		$pagenum = abs($pagenum);
		
		$offset = ($pagenum-1) * $perpage;

		// Get post data
		$data['posts'] = $this->Post_model->getPosts('all', 'array', 'publish', $perpage, $offset, 'created_at', 'desc', 'array',  $author);
		
		// Generate pagination links
		$config['base_url'] = site_url('articles/author/' . $this->getAttribute('author'));
		$config['total_rows'] = $this->Post_model->getPosts('all', 'total', 'publish', $perpage, $offset, 'created_at', 'desc', 'array',  $author);
		$config['per_page'] = $perpage;
		$config['uri_segment'] = $page_segment;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
        
		return $this->output($data);
    }

    public function getPostCategory()
    {
    	$this->load->model('post/Taxonomy_model');
    	$post_id = $this->getAttribute('post_id');
    	return $this->Taxonomy_model->get_category($post_id);
    }

    /**
	 * Search
     * 
     * Searching item across table.
     *
     * @param string $keyword
     * @return object
	 */
	public function search()
	{
        $keyword = $this->input->get('s');
        if(!$keyword) return [];
		
        $this->load->model('post/Search_model');
        $this->load->library('pagination');

        $config = [
            'base_url' => site_url('search?s=' . $keyword),
		    'total_rows' => $this->Search_model->search($keyword, 'total'),
		    'per_page' => $this->getAttribute('per_page', 8),
		    'page_query_string' => TRUE,
		    'query_string_segment' => 'page'
        ];

        $this->pagination->initialize($config);
        
		$data['pagination'] = $this->pagination->create_links();
        $data['results'] = $this->Search_model->search($keyword, 'list', $config['per_page'], $this->input->get('page'));

        return $this->output($data);
    }

	function getPopularPosts()
	{
		$this->load->model('post/PostModel');
		$limit = $this->getAttribute('limit', 5);
		$popular = $this->PostModel
						->where('status','publish')
						->order_by('cast(last_seen as date)','desc')
						->order_by('total_seen','desc')
						->limit($limit)
						->getAll();
		return $popular;
	}

	function recordPostView()
	{
		$post_id = $this->getAttribute('id');

		$this->load->model('post/PostModel');
		$this->PostModel
			 ->where('id', $post_id)
			 ->set('total_seen', 'total_seen+1', false)
			 ->set('last_seen', date('Y-m-d H:i:s'))
			 ->update();
	}
}