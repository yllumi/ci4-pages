<?php

use App\core\REST_Controller;

class Entry extends REST_Controller
{
    private $user;

	public function __construct()
	{
		parent::__construct();

		$this->load->model('Entry_model');
        $this->load->helper('entry');

        $this->entry = $this->uri->segment(3);
        if(!isset(config_item('entries')[$this->entry])) show_404();
        $this->entryConf = config_item('entries')[$this->entry] ?? false;

        // Check jwt if require
        if($this->entryConf['require_auth'] ?? false) 
            $this->user = $this->checkToken();

        $this->Entrydata_model = setup_entry_model($this->entry);
	}

	public function index($entry)
	{
		$pagenum = $this->input->get('page') ?? 1;
		$order_by = $this->input->get('orderby') ?? 'id';
		$order_direction = $this->input->get('direction') ?? 'asc';

        if ($this->entryConf['view_table'] ?? '')
            $this->Entrydata_model->table = $this->entryConf['view_table'];

        // Set config for pagination
        $perpage    = $this->input->get('perpage') ?? 10;
        $uri        = 'api/entry/'.$entry.'/';
        $total_rows = $this->Entrydata_model
                            ->setFilter()
                            ->count_rows();

        $pagination_config = [
            'display_pages' => false,
            'page_query_string' => true
        ];

        // Get data, pagination and field list
        $this->Entrydata_model
             ->setFilter()
             ->order_by($order_by, $order_direction);

        if($this->entryConf['show_on_api'] ?? '')
            $this->Entrydata_model->select('id,'.implode(',',$this->entryConf['show_on_api']));

        $results = $this->Entrydata_model
                        ->order_by('created_at', 'desc')
                        ->paginate($perpage, $total_rows, $pagenum, $uri, $pagination_config);

        $data['total'] = $total_rows;
        $data['results'] = [];
        if($results){
            foreach ($results as $result) {
            	$EntryEntity = new App\modules\entry\models\EntryEntity($this->entry, $result);
            	$data['results'][] = $EntryEntity->asArray();
            }
        }

        $data['pagination']['prev'] = $this->Entrydata_model->previous_page;
        $data['pagination']['next'] = $this->Entrydata_model->next_page;

		$this->response($data);
	}

    // Insert entry data
    public function insert($entry)
    {
        $postdata = json_decode(ci()->input->raw_input_stream, true);
        if(! $postdata) $postdata = $this->input->post(null, true);

        $postdata = $this->prep_before_insert($postdata);

        $result = $this->Entrydata_model
                       ->set_form_data($postdata)
                       ->validate()
                       ->insert();

        if(! $result)
            $this->response([
                'status' => 'failed',
                'message' => strip_tags($this->Entrydata_model->getInvalidMessage())
            ]);
        
        $result = $this->_callback_after_insert($entry, $result);
        $this->response(['status' => 'success', 'data' => $result]);
    }

    // Update entry data
    public function update($entry, $id)
    {
        $postdata = json_decode(ci()->input->raw_input_stream, true);
        if(! $postdata) $postdata = $this->input->post(null, true);

        $result = $this->Entrydata_model
                       ->set_form_data($postdata)
                       ->validate()
                       ->where('id',$id)
                       ->update();

        if(! $result)
            $this->response([
                'status' => 'failed',
                'message' => strip_tags($this->Entrydata_model->getInvalidMessage())
            ]);
    
        $result = $this->_callback_after_update($entry, $postdata, $result);
        $this->response(['status' => 'success', 'message' => 'Data updated']);
    }

	public function detail($entry, $id)
	{
        if ($this->entryConf['view_table'] ?? '')
            $this->Entrydata_model->table = $this->entryConf['view_table'];
            
        if ($this->entryConf['show_on_api'] ?? '')
            $this->Entrydata_model->select('id,' . implode(',', $this->entryConf['show_on_api']));

        if($with = $this->input->get('with', true)){
            $with = explode(',', $with);
            foreach($with as $relation)
                $this->Entrydata_model->{'with_'.$relation}();
        }

        $this->Entrydata_model->where('id', $id);

        if($data = $this->Entrydata_model->get())
            $this->response(array_merge(['status'=>'success'], $data));
        else
            $this->response([
                'status' => 'failed',
                'message' => 'Data not found'
            ]);
	}

    public function detailByField($entry)
	{
        $filter = $this->input->get();

        if ($this->entryConf['view_table'] ?? '')
            $this->Entrydata_model->table = $this->entryConf['view_table'];
            
        if ($this->entryConf['show_on_api'] ?? '')
            $this->Entrydata_model->select('id,' . implode(',', $this->entryConf['show_on_api']));

        if($with = $this->input->get('with', true)){
            $with = explode(',', $with);
            foreach($with as $relation)
                $this->Entrydata_model->{'with_'.$relation}();
        }

        foreach($filter as $field => $value) {
            $this->Entrydata_model->where($field, $value);
        }

        if($data = $this->Entrydata_model->get())
            $this->response(array_merge(['status'=>'success'], $data));
        else
            $this->response([
                'status' => 'failed',
                'message' => 'Data not found'
            ]);
	}

    public function dropdown()
    {
        $sort = $this->input->get('sort') ?? 'created_at';
        $order = $this->input->get('sortdir') ?? 'desc';
        $select = ['id'];
        if($caption = $this->input->get('caption'))
            array_push($select, $caption);

        $results = $this->Entrydata_model
                        ->select($select)
                        ->setFilter()
                        ->order_by($sort,$order)
                        ->getAll();

        $this->response($results);
    }

    private function prep_before_insert($data)
    {
        $fields = $this->entryConf['fields'];
        
        foreach ($fields as $field => $conf) {
            if($field == 'owner')
            $data['owner'] = $this->user->user_id ?? $data['owner'];
            else
            $data[$field] = $data[$field] ?? generate_input_api($conf);
        }
        
        return $data;
    }

    // CALLBACK EVENTS
    // Callback functions
    private function _callback_before_insert($entry, $data)
    {
        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $data;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'beforeInsert'))
        return $actionClass->beforeInsert($data);

        return $data;
    }
    private function _callback_after_insert($entry, $result)
    {
        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $result;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'afterInsert'))
        return $actionClass->afterInsert($result);

        return $result;
    }
    private function _callback_before_update($entry, $data)
    {
        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $data;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'beforeUpdate'))
        return $actionClass->beforeUpdate($data);

        return $data;
    }
    private function _callback_after_update($entry, $data, $affected)
    {
        if (!$affected) return false;

        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $affected;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'afterUpdate'))
        return $actionClass->afterUpdate($data, $affected);

        return $data;
    }

    public function _callback_before_delete($entry, $id)
    {
        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $id;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'beforeDelete'))
        return $actionClass->beforeDelete($id);
    }

    public function _callback_after_delete($entry, $id, $affected)
    {
        if (!$affected) return false;

        $EntryActionFile = ucfirst($entry) . 'EntryAction';
        if (!file_exists(config_item('entries')[$entry]['path'] . '/Action.php'))
        return $affected;

        include_once(config_item('entries')[$entry]['path'] . '/Action.php');
        $actionClass = new $EntryActionFile();

        if (method_exists($actionClass, 'afterDelete'))
        return $actionClass->afterDelete($id);
    }
    
}
