<?php namespace App\modules\entry\libraries;

class Entry {

    public $entry;
	
	public $index_url;
	public $add_url;
	public $edit_url;
	public $delete_url;
    
    public $entryConf;
	public $Entrydata_model;
    public $ActionClass;
	
	public $exportCsvLink;

	public function __construct($entry)
	{
        $this->entry = $entry;

        if(!isset(config_item('entries')[$this->entry])) show_404();
        $this->entryConf = config_item('entries')[$this->entry] ?? false;

        if(!ci()->db->table_exists($this->entryConf['table']))
            show_error('Entry not installed.', 200);

        $this->Entrydata_model = setup_entry_model($this->entry);

        // Set URLs
        $this->setUrl();
        $this->exportCsvLink = site_url("admin/entry/$entry/export_csv?".$_SERVER['QUERY_STRING']);

        // Prepare ActionClass
        if (file_exists($this->entryConf['path'] . '/Action.php')) {
            $classname = ucfirst($this->entry) . 'EntryAction';
            include_once($this->entryConf['path'] . '/Action.php');
            $this->ActionClass = new $classname();
            ci()->shared['ActionClass'] = &$this->ActionClass;
        }
	}

	public function setUrl($indexUri = null, $addUri = null, $editUri = null, $deleteUri = null)
	{
		$this->index_url  = $indexUri ?? $this->entryConf['custom_url'];
		$this->add_url 	  = $addUri ?? "admin/entry/{$this->entry}/add";
		$this->edit_url   = $editUri ?? "admin/entry/{$this->entry}/edit";
		$this->delete_url = $deleteUri ?? "admin/entry/{$this->entry}/delete";
	}

	public function table()
	{
		$where_in_by_join = [];
        $exclude_filter = [];

        // Use view_table for table if set
        if($this->entryConf['view_table'] ?? '')
            $this->Entrydata_model->table = $this->entryConf['view_table'];

        // Set config for pagination
        $perpage    = ci()->input->get('perpage') ?? $this->entryConf['row_per_page'] ?? 10;
        $uri        = $this->index_url;
        $total_rows = $this->Entrydata_model
                            ->setFilter($exclude_filter, $where_in_by_join)
                            ->count_rows();

        // Join model table
        // TODO: Get has_one from field with relation
        if(!empty($this->Entrydata_model->has_one) && ! ($this->entryConf['view_table'] ?? ''))
            foreach ($this->Entrydata_model->has_one as $relation_entry => $opt) {
                $withFunction = 'with_'.$relation_entry;
                $this->Entrydata_model->$withFunction();
            }

        // Get data, pagination and field list
        $this->Entrydata_model->setFilter($exclude_filter, $where_in_by_join);

        $sort = $_GET['sort'] ?? $this->entryConf['default_sorting'][0] ?? 'created_at';
        $sortdir = $_GET['sortdir'] ?? $this->entryConf['default_sorting'][1] ?? 'desc';
        $data['results'] = $this->Entrydata_model
                                ->order_by($sort, $sortdir)
                                ->paginate($perpage, $total_rows, $_GET['page'] ?? 1, $uri, ['page_query_string'=>true]);
        $data['pagination'] = $this->Entrydata_model->all_pages;
        $data['show_on_table'] = $this->entryConf['show_on_table'] ?? array_keys($this->entryConf['fields']);
        $data['fullwidth_table'] = $this->entryConf['fullwidth_table'] ?? true;
        $data['small_table'] = $this->entryConf['small_table'] ?? true;
        $data['show_timestamps'] = $this->entryConf['show_timestamps'] ?? false;
        
        $data['sorting'] = $this->entryConf['sorting'] ?? true;
        $data['perpaging'] = $this->entryConf['perpaging'] ?? true;
        $data['filtering'] = $this->entryConf['filtering'] ?? true;
        $data['show_numbering'] = $this->entryConf['show_numbering'] ?? true;
        $data['show_total'] = $this->entryConf['show_total'] ?? true;
        
        $data['fields'] = $this->entryConf['view_fields'] ?? $this->entryConf['fields'];
        $data['action_buttons'] = $this->entryConf['action_buttons'] ?? false;
        
        $data['entry'] = $this->entry;
        $data['entryConf'] = $this->entryConf;
        $data['uri'] = $uri;
        $data['total'] = $total_rows;

        $data['index_url'] = $this->index_url;
        $data['add_url'] = $this->add_url;
        $data['edit_url'] = $this->edit_url;
        $data['delete_url'] = $this->delete_url;

        if(isset($this->entryConf['parent_module']) 
           && isset($this->entryConf['parent_module_filter_field'])
           && ($_GET['filter'][$this->entryConf['parent_module_filter_field']] ?? '') )
        {
            $parentModuleFilterFieldID = $_GET['filter'][$this->entryConf['parent_module_filter_field']];
            $data['parent_module_filter_field'] = $this->entryConf['parent_module_filter_field'];
            $data['reset_link'] = site_url('admin/entry/'.$this->entry.'/?filter['.$this->entryConf['parent_module_filter_field'].']='.$parentModuleFilterFieldID);
        }

        return ci()->load->view('entry/admin/template/table', $data, true);
	}

	public function form($id = null)
	{
		// Process post data
        if(ci()->input->post())
        	$id ? $this->_update($id) : $this->_insert();

        $data['entry'] = $this->entry;
        $data['entryConf'] = $this->entryConf;
        $data['fields'] = $this->entryConf['fields'];
        $data['index_url'] = $this->index_url;
        $data['add_url'] = $this->add_url;

        if($id)
        {
	        $EntryRel_model = [];
	        foreach ($this->entryConf['fields'] as $field => $fieldConf) {
	            if(isset($fieldConf['relation']['entry'])){
	                $withFunction = 'with_'.$fieldConf['relation']['entry'];
	                $this->Entrydata_model->$withFunction();
	            }
	        }
            
	        $data['result'] = $this->Entrydata_model->get($id);
        	$data['id'] = $id;
        }

		return ci()->load->view('admin/template/form', $data, true);
	}

	private function _insert()
    {
    	ci()->load->model('entry/Entry_model');
        if($result = ci()->Entry_model->insert($this->entry))
        {
            ci()->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
        } else {
            ci()->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
            redirect(getenv('HTTP_REFERER'));
        } 

        if(ci()->input->post('submitBtn') == 'save_and_exit')
            redirect($this->index_url.'?'.$_SERVER['QUERY_STRING']);
        else
            redirect($this->edit_url.'/'.$result['id'].'?'.$_SERVER['QUERY_STRING']);
    }

    private function _update($id)
    {
        if(!$id) throw new \Exception('ID not defined for updating.');
        
    	ci()->load->model('entry/Entry_model');
        if($result = ci()->Entry_model->update($this->entry, $id))
        {
            ci()->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
        } else {
            ci()->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
        } 

        if(ci()->input->post('submitBtn') == 'save_and_exit')
            redirect($this->index_url.'?'.$_SERVER['QUERY_STRING']);
        else
            redirect($this->edit_url.'/'.$id.'?'.$_SERVER['QUERY_STRING']);
    }

    public function delete($id = false)
    {
        if(!$id) throw new \Exception('ID not defined for deletion.');

        ci()->load->model('entry/Entry_model');
        ci()->Entry_model->_callback_before_delete($this->entry, $id);
        $affected = $this->Entrydata_model->delete($id);
        ci()->Entry_model->_callback_after_delete($this->entry, $id, $affected);

        return $affected;
    }

}
