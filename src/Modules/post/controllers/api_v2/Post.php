<?php

use App\core\REST_Controller;

class Post extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->model('post/Taxonomy_model');
		$this->load->model('post/Post_model');
        
		// Retrieve extension attributes
		$user = $this->input->get('user', true) ?? 'all';
		$type = $this->input->get('type', true) ?? 'post';
		$status = $this->input->get('status', true) ?? 'publish';
		$orderby = $this->input->get('orderby', true) ?? 'created_at';
		$sort = $this->input->get('sort', true) ?? 'desc';
		$page = $this->input->get('page', true) ?? 1;
		
		$perpage = $this->input->get('perpage', true) ?? 8;
		$offset = ($page-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getPosts($type, 'loop', $status, $perpage, $offset, $orderby, $sort, 'array', $user);
        
        // Inject category ..
        foreach($posts as $key => $post)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($post['id']);
            $posts[$key]['url'] = site_url('api/post/'.$post['id']);

            if(strpos($post['featured_image'], 'http') === false){
            	$this->load->helper('media/filemanager');
            	$posts[$key]['featured_image'] = get_file_url($post['featured_image']);
            }
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = 'api/post';
		$config['total_rows'] = $this->Post_model->getPosts($type, 'total', $status, null, null, null, null, null, $user);
		$config['per_page'] = $perpage;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_next_link();
		$data['page_links']['prev'] = $this->pagination->create_prev_link();
		$data['page_links']['next'] = $this->pagination->create_next_link();

		$this->response([
			'response_code'    => REST_Controller::HTTP_OK,
			'response_message' => 'success',
			'data'			   => $data 
		]);
	}

	public function category($category = 'all')
	{
		$this->load->model('post/Taxonomy_model');
		$this->load->model('post/Post_model');
        
		$category_exclude = explode(',',$this->input->get('exclude', true) ?? '');
		
		// Retrieve extension attributes
		$user = $this->input->get('user', true) ?? 'all';
		$type = $this->input->get('type', true) ?? 'post';
		$status = $this->input->get('status', true) ?? 'publish';
		$orderby = $this->input->get('orderby', true) ?? 'created_at';
		$sort = $this->input->get('sort', true) ?? 'desc';
		
		$page = $this->input->get('page', true) ?? 1;
		$perpage = $this->input->get('perpage', true) ?? 8;
		
		$offset = ($page-1) * $perpage;

		// Get post data
		$posts = $this->Post_model->getPostsByCategory($category, $type, 'loop', $status, $perpage, $offset, 'array', $category_exclude);
        
        // Inject category ..
        foreach($posts as $key => $post)
        {
            $posts[$key]['category'] = $this->Taxonomy_model->get_category($post['id']);
            $posts[$key]['url'] = site_url('api/post/'.$post['id']);
        }

        // Get tag and category information
        $data['posts'] = $posts;

		// Generate pagination links
		$config['base_url'] = 'api/post/category/'.$category;
		$config['total_rows'] = $this->Post_model->getPostsByCategory($category, $type, 'total', $status, null, null, null, $category_exclude);
		$config['per_page'] = $perpage;
		$config['use_page_numbers'] = TRUE;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_next_link();
		$data['page_links']['prev'] = $this->pagination->create_prev_link();
		$data['page_links']['next'] = $this->pagination->create_next_link();

		$this->response([
			'response_code'    => REST_Controller::HTTP_OK,
			'response_message' => 'success',
			'data'			   => $data 
		]);
	}

	public function detail($id)
	{
		$this->load->model('post/Post_model');

		$field = $this->input->get('field') ?? 'id';
        $post = $this->Post_model->getPost(null, $field, $id, 'array');
        if(!$post)
        	$this->response([
				'response_code'    => REST_Controller::HTTP_NOT_FOUND,
				'response_message' => 'Post not found',
			]);

        if($post['content_type'] == 'markdown'){
	        $Parsedown = new ParsedownExtra();
			$post['content'] = $Parsedown->setBreaksEnabled(true)->text($post['content']);
        }
        
		$post['published_at'] = time_ago($post['published_at']);

		if(strpos($post['featured_image'], 'http') === false){
        	$this->load->helper('media/filemanager');
        	$post['featured_image'] = get_file_url($post['featured_image']);
        }
        
        $this->response([
			'response_code'    => REST_Controller::HTTP_OK,
			'response_message' => 'success',
			'data'			   => $post 
		]);
	}

}		 
