<?php

use App\core\Backend_Controller;
use Symfony\Component\Yaml\Yaml;

class Config extends Backend_Controller {

    private $entryDisableFile = SITEPATH.'configs/disabled_entries.json';

    private $configFields;
    private $basicConfig = [];
    private $fieldsConfig = [];
    private $queryConfig = [];

	public function __construct()
	{
        parent::__construct();

        $this->load->helper('entry/entry');

        if(!file_exists($this->entryDisableFile)){
            file_put_contents($this->entryDisableFile, '[]');
        }

        $this->initConfigFields();
        $this->initConfigArray();
    }

    private function initConfigFields()
    {
        $basicLeftFields = [
            'name' => ['field' => 'name', 'label' => 'Nama Entry', 'form' => 'text'],
            'description' => ['field' => 'description', 'label' => 'Deskripsi', 'form' => 'textarea'],
            'icon' => ['field' => 'icon', 'label' => 'Icon', 'form' => 'text'],
            'table' => ['field' => 'table', 'label' => 'Nama DB Table', 'form' => 'text'],
            'row_per_page' => ['field' => 'row_per_page', 'label' => 'Default Row Per Page', 'form' => 'number', 'default' => 10],
            'show_admin_menu' => ['field' => 'show_admin_menu', 'label' => 'Tampilkan Menu di Admin?', 'form' => 'switch', 'options' => [0 => 'Tidak', 1 => 'Ya']],
            'parent_menu' => ['field' => 'parent_menu', 'label' => 'Menu Induk', 'form' => 'text'],
            'custom_url' => ['field' => 'custom_url', 'label' => 'URL Kustom', 'form' => 'text'],
            'menu_position' => ['field' => 'menu_position', 'label' => 'Posisi Menu', 'form' => 'text'],
            'parent_module' => ['field' => 'parent_module', 'label' => 'Parent Entry', 'form' => 'text'],
            'parent_module_filter_field' => ['field' => 'parent_module_filter_field', 'label' => 'Parent Entry Filter Field', 'form' => 'text'],
            'action_buttons' => ['field' => 'action_buttons', 'label' => 'Buttons Configuration', 'form' => 'code', 'mode' => 'yaml', 'height' => 350],
            'setting' => ['field' => 'setting', 'label' => 'Entry Settings', 'form' => 'code', 'mode' => 'yaml', 'height' => 350],
        ];

        $basicRightFields = [
            'export_csv' => ['field' => 'export_csv', 'label' => 'Enable Export to CSV', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 0],
            'filtering' => ['field' => 'filtering', 'label' => 'Show Filtering Form', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'sorting' => ['field' => 'sorting', 'label' => 'Show Sorting Option', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'perpaging' => ['field' => 'perpaging', 'label' => 'Show Perpage Option', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'fullwidth_table' => ['field' => 'fullwidth_table', 'label' => 'Table Width', 'form' => 'switch', 'options' => [0 => 'Responsive', 1 => 'Fullwidth'], 'default' => 1],
            'show_numbering' => ['field' => 'show_numbering', 'label' => 'Show Numbering', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'show_total' => ['field' => 'show_total', 'label' => 'Show Total', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'small_table' => ['field' => 'small_table', 'label' => 'Use Small Table', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 1],
            'show_timestamps' => ['field' => 'show_timestamps', 'label' => 'Show Timestamp Columns', 'form' => 'checkbox', 'options' => ['created_at' => 'Created At', 'updated_at' => 'Updated At']],
            'disable_detail' => ['field' => 'disable_detail', 'label' => 'Disable Detail Popup', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 0],
            'hide_edit' => ['field' => 'hide_edit', 'label' => 'Hide Edit Button', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 0],
            'disable_edit' => ['field' => 'disable_edit', 'label' => 'Disable Edit Button', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 0],
            'disable_delete' => ['field' => 'disable_delete', 'label' => 'Disable Delete Button', 'form' => 'switch', 'options' => [0 => 'Off', 1 => 'On'], 'default' => 0],
            'show_on_table' => ['field' => 'show_on_table', 'label' => 'Kolom di Halaman Tabel', 'form' => 'code', 'mode' => 'yaml', 'height' => 200],
            'show_on_api' => ['field' => 'show_on_api', 'label' => 'Kolom di API', 'form' => 'code', 'mode' => 'yaml', 'default' => '', 'height' => 200],
            'privileges' => ['field' => 'privileges', 'label' => 'Permission List', 'form' => 'code', 'mode' => 'yaml', 'height' => 200],
        ];

        $this->configFields = [
            'basicLeftFields' => $basicLeftFields,
            'basicRightFields' => $basicRightFields
        ];
    }

    private function initConfigArray()
    {
        $entry = ci()->uri->segment(5);
        if(!$entry) return false;

        if(!isset(config_item('entries')[$entry]))
            show_error("Entry not found: $entry", 401);
            
        $entryConf = config_item('entries')[$entry];

        // Assign all config array
        $this->fieldsConfig = $entryConf['fields'];

        $basicConfigKeys = array_merge(array_keys($this->configFields['basicLeftFields']), array_keys($this->configFields['basicRightFields']));
        foreach ($basicConfigKeys as $key)
            if(isset($entryConf[$key]))
                $this->basicConfig[$key] = $entryConf[$key];
    }
    
	public function index()
	{
        checkPermission('entry:show');

		$data['page_title'] = 'Entries Configuration';
        $data['action'] = site_url('admin/entry/config/update');
        $data['results'] = config_item('entries');


        $data['hierarchy'] = [];
        foreach($data['results'] as $entry => $entryConf)
            $data['hierarchy'][$entryConf['module']??'standalone'][] = $entry;

        // Silly but I have to move it down
        $standalone = $data['hierarchy']['standalone'];
        unset($data['hierarchy']['standalone']);
        $data['standalone'] = $standalone;

		$this->view('admin/config/list', $data);
	}

    public function enable($entry)
    {
        checkPermission('entry:enable');

        $disabledModules = json_decode(file_get_contents($this->entryDisableFile), true);
        if (($key = array_search($entry, $disabledModules)) !== false) {
            unset($disabledModules[$key]);
        }
        file_put_contents($this->entryDisableFile, json_encode(array_values($disabledModules),JSON_PRETTY_PRINT));
        redirect(getenv('HTTP_REFERER'));
    }

    public function disable($entry)
    {
        checkPermission('entry:enable');

        $disabledModules = json_decode(file_get_contents($this->entryDisableFile), true);
        $disabledModules[] = $entry;
        file_put_contents($this->entryDisableFile, json_encode(array_values($disabledModules),JSON_PRETTY_PRINT));
        redirect(getenv('HTTP_REFERER'));
    }

    public function sync($entry)
    {
        checkPermission('entry:build_table');

        if(!$yaml = config_item('entries')[$entry] ?? '') {
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Entry not found.</div>');
            redirect('admin/entry/config');
        }

        if($this->Entry_model->sync($entry, $yaml)){
            $this->session->set_flashdata('message', '<div class="alert alert-success">Entry structure <strong>'.$entry.'</strong> synced successfully.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Entry structure <strong>'.$entry.'</strong> fail to synced.</div>');
        }

        redirect(getenv('HTTP_REFERER'));
    }

    public function create_view($entry)
    {
        checkPermission('entry:build_view');

        if(!$yaml = config_item('entries')[$entry] ?? '') {
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Entry not found.</div>');
            redirect('admin/entry/config');
        }

        if(! ($yaml['view_query'] ?? '')){
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Entry view query undefined.</div>');
            redirect('admin/entry/config');
        }

        ci()->db->query("DROP VIEW IF EXISTS `{$yaml['view_table']}`");
        $result = ci()->db->query("CREATE VIEW `{$yaml['view_table']}` AS " . $yaml['view_query']);

        if($result){
            $this->session->set_flashdata('message', '<div class="alert alert-success">Entry view query for entry <strong>'.$entry.'</strong> created.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Entry view query <strong>'.$entry.'</strong> fail to created.</div>');
        }

        redirect(getenv('HTTP_REFERER'));
    }

    public function getSelect2Dropdown()
    {
        $this->output->enable_profiler(false);
        $this->output->set_content_type('application/json');

        $foreign_key = $this->input->get('fkey') ?? 'id';

        $keyword = $this->input->get('keyword', true);
        $table = $this->input->get('table', true);
        $caption_field = $this->input->get('caption_field', true);
        $search_field = $this->input->get('search_field', true);
        
        if(! $keyword)
            $results = [];
        
        else {
            $this->db->select($foreign_key. ', ' . implode(',',$search_field))
                     ->from($table);

            foreach ($search_field as $sfield)
                $this->db->or_like($sfield, $keyword, 'both');

            $results = $this->db->limit(10)
                                ->get()
                                ->result_array();
        }

        $data['results'] = [];
        if($results){
            foreach ($results as $value) {
                $text = '';
                foreach ($caption_field as $cfield)
                    $text .= $value[$cfield] ?? $cfield;

                $data['results'][] = [
                    'id' => is_binary($value[$foreign_key]) ? bin2uuid($value[$foreign_key]) : $value[$foreign_key],
                    'text' => $text,
                ];
            }
        }

        echo json_encode($data);
    }

    public function configure($entry)
    {
        checkPermission('entry:configure');

        if($postdata = ci()->input->post(null, true)){
            $postdata['show_on_api'] = Yaml::parse($postdata['show_on_api']);
            $postdata['show_on_table'] = Yaml::parse($postdata['show_on_table']);
            $postdata['privileges'] = Yaml::parse($postdata['privileges']);
            $postdata['action_buttons'] = Yaml::parse($postdata['action_buttons']);
            $postdata['setting'] = Yaml::parse($postdata['setting']);

            // Move position
            $setting = $postdata['setting'];
            unset($postdata['setting']);
            $postdata += ['setting' => $setting];
            $action_buttons = $postdata['action_buttons'];
            unset($postdata['action_buttons']);
            $postdata += ['action_buttons' => $action_buttons];

            // Set numeric value as integer
            foreach ($postdata as $key => $post) {
                if(is_numeric($post))
                    $postdata[$key] = intval($post);
            }
            
            $this->basicConfig = $postdata;
            $this->saveConfiguration();
        }

        $data['entry'] = $entry;
        $data['entryConf'] = config_item('entries')[$entry];

        if($data['entryConf']['action_buttons'] ?? null)
            $data['entryConf']['action_buttons'] = Yaml::dump($data['entryConf']['action_buttons'], 5, 2);
        if($data['entryConf']['setting'] ?? null)
            $data['entryConf']['setting'] = Yaml::dump($data['entryConf']['setting'], 5, 2);

        $data['configFields'] = $this->configFields;

        $this->view('admin/config/configure', $data);
    }

    public function fields($entry)
    {
        checkPermission('entry:configure');
        $data['entry'] = $entry;
        $data['entryConf'] = config_item('entries')[$entry];

        $this->view('admin/config/fields', $data);
    }

    private function saveConfiguration()
    {
        $configArray = array_merge($this->basicConfig, ['fields' => $this->fieldsConfig], $this->queryConfig);
        $configYaml = Yaml::dump($configArray, 5);
        dd($configYaml);
    }

}
