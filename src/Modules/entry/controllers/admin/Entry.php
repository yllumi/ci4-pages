<?php

use App\core\Backend_Controller;

class Entry extends Backend_Controller {
    
    private $entry;
    private $entryConf;

    private $Entry;

    public function __construct()
    {
        parent::__construct();

        $this->entry = $this->uri->segment(3);
        if(!isset(config_item('entries')[$this->entry])) show_404();

        $this->Entry = new App\modules\entry\libraries\Entry($this->entry);
        $this->entryConf = $this->Entry->entryConf;
        
        $this->shared['current_module'] = $this->entry;
    }
    
    public function index()
    {
        if (($this->Entry->ActionClass ?? null) && method_exists($this->Entry->ActionClass, 'overrideFilter'))
            $this->Entry->ActionClass->overrideFilter();        

        $data['table'] = $this->Entry->table();
        $data['add_url'] = $this->Entry->add_url;

        // Redirect to parent if not allow without parent
        if(isset($this->entryConf['parent_module'])
          && ($this->entryConf['allow_without_parent'] ?? true) == false
          && !isset($_GET['filter'][$this->entryConf['parent_module_filter_field']]))
            redirect('admin/entry/'. $this->entryConf['parent_module']);

        // Get Parent Data
        if(isset($this->entryConf['parent_module']) 
           && isset($this->entryConf['parent_module_filter_field'])
           && ($_GET['filter'][$this->entryConf['parent_module_filter_field']] ?? '') )
        {
            $params = [
                $this->entryConf['parent_module'],
                $this->entryConf['parent_module_filter_field']
            ];

            if(($this->Entry->ActionClass ?? null) && method_exists($this->Entry->ActionClass, 'setParentData'))
            $data['parent_data'] = call_user_func_array([$this->Entry->ActionClass, 'setParentData'], $params);        
            
            if(($this->Entry->ActionClass ?? null) && method_exists($this->Entry->ActionClass, 'setBreadcrumbs'))
                $data['parent_url'] = call_user_func_array([$this->Entry->ActionClass, 'setBreadcrumbs'], $params);
        }

        $data['entry'] = $this->entry;
        $data['entryConf'] = $this->entryConf;
        $data['page_title'] = $this->entryConf['name'];
        $data['action_buttons'] = $this->entryConf['action_buttons'] ?? false;

        $this->view('admin/entry/index', $data);
    }

    public function add()
    {
        // Create and process insert form
        $data['form'] = $this->Entry->form();

        $data['page_title'] = 'New '.$this->entryConf['name'];
        $data['method'] = 'add';
        
		$this->view('admin/entry/form', $data);
    }

    public function detail($entry, $id = false)
    {
        if (!$id) show_404();

        $data['page_title'] = 'Detail ' . $this->entryConf['name'];

        $data['entry'] = $this->entry;
        $data['entryConf'] = $this->entryConf;
        $data['fields'] = $this->entryConf['fields'];
        $data['index_url'] = $this->Entry->index_url;
        $data['add_url'] = $this->Entry->add_url;

        if ($id) {
            $EntryRel_model = [];
            foreach ($this->entryConf['fields'] as $field => $fieldConf) {
                if (isset($fieldConf['relation']['entry'])) {
                    $withFunction = 'with_' . $fieldConf['relation']['entry'];
                    $this->Entry->Entrydata_model->$withFunction();
                }
            }

            $data['result'] = $this->Entry->Entrydata_model->get($id);
            $data['id'] = $id;
        }

        // dd($data);
        $data['layout'] = 'blank';
        $this->view('admin/template/detail', $data);
    }
    
    public function edit($entry, $id = false)
	{
        if(!$id) show_404();
        
        // Create and process update form
        $data['form'] = $this->Entry->form($id);
        
        $data['page_title'] = 'Edit '.$this->entryConf['name'];
        $data['method'] = 'edit';
        
        $this->view('admin/entry/form', $data);
    }

    public function delete($entry = false, $id = false)
    {
        if(!$entry || !$id) show_404();

        if($this->Entry->delete($id))
        {
            $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully deleted.</div>');
        } else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete data.</div>');

        redirect($this->Entry->index_url.'?'.$_SERVER['QUERY_STRING']);
    }

    public function confirm($entry = false, $type = 'row', $action = null, $id = null)
    {
        $data['entry'] = $entry;
        $data['entryConf'] = $this->entryConf;
        $data['action'] = $this->entryConf['action_buttons'][$type][$action];
        $data['fields'] = $this->entryConf['action_buttons'][$type][$action]['confirm']['fields'];
        $data['redirect'] = getenv('HTTP_REFERER');

        if($post = $this->input->post()){
            return $this->action($entry, $type, $action, $post);
        }

        $this->view('admin/entry/confirm_form', $data);
    }

    public function export_csv($entry = false)
    {
        $where_in_by_join = [];
        $exclude_filter = [];

        // Use view_table for table if set
        if ($this->entryConf['view_table'] ?? '')
            $this->Entry->Entrydata_model->table = $this->entryConf['view_table'];

        // Join model table
        if(!empty($this->Entry->Entrydata_model->has_one))
            foreach ($this->Entry->Entrydata_model->has_one as $relation_entry => $opt) {
                $withFunction = 'with_'.$relation_entry;
                $this->Entry->Entrydata_model->$withFunction();
            }

        // Get data, pagination and field list
        $this->Entry->Entrydata_model->setFilter($exclude_filter, $where_in_by_join);
        $results = $this->Entry->Entrydata_model->order_by('created_at', 'desc')->get_all();
        $fields = $this->entryConf['show_on_export'] 
                    ?? $this->entryConf['show_on_table'] 
                    ?? array_keys($this->entryConf['fields']);

        $fp = fopen(SITEPATH.'resources/csv/export_'.$entry.'_'.date("YmdHi").'.csv', 'w');

        fputcsv($fp, array_merge($fields, ['created_at']));
        foreach ($results as $result) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = trim(preg_replace('/\s+/', ' ', strip_tags(generate_output($this->Entry->entryConf['fields'][$field], $result))));
                if($this->Entry->entryConf['fields'][$field]['form'] == 'numeric')
                    $row[$field] = "'".$row[$field];
            }
            // Add created_at
            $row['created_at'] = date("d-m-Y H:i", strtotime($result['created_at']));
            fputcsv($fp, $row);
        }
        
        fclose($fp);

        $this->load->helper('download');
        force_download(SITEPATH.'resources/csv/export_'.$entry.'_'.date("YmdHi").'.csv', null);
    }

    // Catch action from custom action button 
    public function action($entry = false, $type = false, $action_name = false)
    {
        if(!$entry || !$type || !$action_name) show_404();

        $params = array_slice($this->uri->segment_array(), 6);
        array_unshift($params, $this->Entry->Entrydata_model);

        $redirect = getenv('HTTP_REFERER');

        // Catch post data from confirm form
        if($post = $this->input->post()){
            array_push($params, $post);
            $redirect = $this->input->post('redirect') ?? $redirect;

            // Clear postdata to prevent merusak operasi di model
            $_POST = [];
        }

        $classname = ucfirst($entry).'EntryAction';
        $methodName = $type.'action_'.$action_name;

        $output = call_user_func_array([$this->Entry->ActionClass, $methodName], $params);

        if($output)
            $this->session->set_flashdata('message', $output['message']);
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Action method not return message output.</div>');
        
        redirect($redirect);
    }
    
    public function update_relation()
	{
        // Get post.
        $post = $this->input->post();
        
        // Init.
        $relation_table = 'mein_entries_' . $post['entry'] . '_' . $post['relation'];
        $first_field = $post['entry'] . '_id';
        $second_field = $post['relation'] . '_id';

        // Trial, dirty code.
        if (!empty($post['choosen']))
        {
            foreach($post['choosen'] as $choosen => $value)
            {
                // Check redundancy
                $this->db->select('id');
                $this->db->from($relation_table);
                $this->db->where($first_field, $post['id']);
                $this->db->where($second_field, $value);
                
                $result = $this->db->get()->row();

                if (empty($result))
                {
                    $this->db->insert($relation_table, [
                        $first_field => $post['id'],
                        $second_field => $value,
                        'created_at' => date('y-m-d h:i:s')
                    ]);
                }
            }
        }

        echo 'done';
    }

    public function remove_relation($entry, $relation, $relation_id)
	{
        $this->load->model('Entry_model');
        $this->Entry_model->remove_relation($entry, $relation, $relation_id);

        $this->session->set_flashdata('message', '<div class="alert alert-success">Sucessfully removed.</div>');

        redirect($_GET['callback']);
    }
}
