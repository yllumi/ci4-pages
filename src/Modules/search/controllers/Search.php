<?php

use App\core\Frontend_Controller;
use Symfony\Component\Yaml\Yaml;

class Search extends Frontend_Controller {

	public function __construct()
	{
        parent::__construct();
        $this->load->model('Search_model');
    }
    
    /**
     * Redirect to search component.
     */
    public function index()
    {
        $q = $this->input->get('q', true) ?? '';
        $type = $this->input->get('type', true) ?? 'post';
        $datatype = Yaml::parse(setting_item('search.datatype')) ?? ['post' => 'Artikel'];
        if(!isset($datatype[$type])) show_404();

        switch ($type) {
            case 'post':
                $result = $this->Search_model->getAll($type, $q, $datatype[$type]);
                break;
            case 'product':
                $result = $this->Search_model->getProducts($q);
                break;
            case 'video':
                $result = $this->Search_model->getVideos($q);
                break;
        }
        $dataconfig = $datatype[$type];

        $this->load->render('search', compact('q','type','datatype','dataconfig','result'));
    }

}
