<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Symfony\Component\Yaml\Yaml;

/**
 * Entry module helpers 
 */

if (!function_exists('generate_input'))
{
    function generate_input($config, $value = null)
    {
        if(is_null($value)) {
            $value = set_value($config['field'], $config['default'] ?? '', false);
            
            // Use value from query string
            if($config['default_query_string'] ?? '' && $_GET[$config['field']] ?? '')
                $value = $_GET[$config['field']] ?? $value;
        }

        $config['form'] = $config['form'] ?? 'text';
        if(file_exists(APPPATH.'modules/entry/views/fields/' . $config['form'] . '/input.php'))
            return ci()->load->view('entry/fields/' . $config['form'] . '/input', compact('config','value'), true);
        
        return ci()->load->view('entry/fields/text/input', compact('config','value'), true);
    }
}

if (!function_exists('generate_input_api')) {
    function generate_input_api($config, $value = null)
    {
        if (is_null($value)) {
            $value = set_value($config['field'], $config['default'] ?? '', false);

            // Use value from query string
            if ($config['default_query_string'] ?? '' && $_GET[$config['field']] ?? '')
                $value = $_GET[$config['field']] ?? $value;
        }

        $config['form'] = $config['form'] ?? 'text';
        if (file_exists(APPPATH . 'modules/entry/views/fields/' . $config['form'] . '/input_api.php'))
            return ci()->load->view('entry/fields/' . $config['form'] . '/input_api', compact('config', 'value'), true);

        return $config['default'] ?? '';
    }
}

if (!function_exists('generate_output'))
{
    function generate_output($config, $result)
    {
        $config['form'] = $config['form'] ?? 'text';
        if(file_exists(APPPATH.'modules/entry/views/fields/' . $config['form'] . '/output.php'))
            return ci()->load->view('entry/fields/' . $config['form'] . '/output', compact('config','result'), true);

        return ci()->load->view('entry/fields/text/output', compact('result','config'), true);
    }
}

if (!function_exists('generate_output_api'))
{
    function generate_output_api($config, $result)
    {
        $ci = get_instance();
        $config['form'] = $config['form'] ?? 'text';
        if(file_exists(APPPATH.'modules/entry/views/fields/' . $config['form'] . '/output_api.php'))
            $filepath = APPPATH.'modules/entry/views/fields/' . $config['form'] . '/output_api.php';
        else
            $filepath = APPPATH.'modules/entry/views/fields/text/output_api.php';

        return (include $filepath);
    }
}

if (!function_exists('form_filter'))
{
    function form_filter($config)
    {
        $ci = get_instance();
        $config['form'] = $config['form'] ?? 'text';
        if(file_exists(APPPATH.'modules/entry/views/fields/' . $config['form'] . '/filter.php'))
            return $ci->load->view('entry/fields/' . $config['form'] .'/filter', compact('config'), true);

        return $ci->load->view('entry/fields/text/filter', compact('config'), true);
    }
}

function register_all_entries()
{
    ci()->config->config['entries'] = cache()->get('entries') ?? [];
    
    if(!ci()->config->config['entries'])
    {
        ci()->load->config('entry/config');

        // Get disabled entries
        $disabled = [];
        if(file_exists(SITEPATH.'configs/disabled_entries.json')){
            $disabled = json_decode(file_get_contents(SITEPATH.'configs/disabled_entries.json'), true);
            $disabled = array_flip($disabled);
        }

        // Register entries inside modules
        $modules = config_item('module_paths');
        foreach ($modules as $module => $paths) {
            if(($paths[2] ?? false) && file_exists($paths[0].'entries/')) {
                $entries = directory_map($paths[0].'entries/', 1);
                foreach ($entries as $entry) {
                    if(config_item('modules')[$module]['enable'] === false) continue;
                    register_entry(trim($entry, DIRECTORY_SEPARATOR), $paths[0].'entries/'.$entry, isset($disabled[trim($entry, DIRECTORY_SEPARATOR)]) ? true : false, $module);
                }
            }
        }

        // Register standalone entries
        $entries = config_item('entry_paths');
        foreach ($entries as $entry => $entryFolder) {
            register_entry($entry, $entryFolder, isset($disabled[trim($entry, DIRECTORY_SEPARATOR)]) ? true : false);
        }

        if($_ENV['CI_ENV'] == 'production')
            cache()->set('entries', ci()->config->config['entries'], 24*60*60);
    }
}

function register_entry($entry, $entryFolder, $disable = false, $module = null)
{
    if(!file_exists($entryFolder.'schema.yml')) return false;

    $yaml = file_get_contents($entryFolder.'schema.yml');
    $res = \Symfony\Component\Yaml\Yaml::parse($yaml);

    // Set enable status
    $res['enable'] = $disable ? false : true;

    // Set under which module
    $res['module'] = $module;

    // Set default privileges
    $res['privileges'] = array_merge(['read','insert','update','delete','export_csv'], $res['privileges'] ?? []);

    // Add custom url and custom menu 
    // for compatibility with admin menu
    if(!isset($res['custom_url']))
        $res['custom_url'] = "admin/entry/{$entry}/";

    // Define owner id field
    if($res['set_owner'] ?? false)
        $res['fields']['owner'] = [
            'field' => 'owner',
            'label' => 'Owner',
            'form' => 'owner',
            'type' => 'int',
            'null' => true,
            'hide_label' => true,
            'relation' => [
                'entry' => 'user',
                'caption' => ['name','email'],
                'model' => 'User_model',
                'model_path' => 'user/User_model',
                'foreign_key' => 'id',
                'local_key' => 'owner'
            ]
        ];

    // Set entry path
    $res['path'] = $entryFolder;

    if(! ci()->config->config['entries'])
        ci()->config->config['entries'] = [];

    ci()->config->config['entries'] = array_merge(ci()->config->config['entries'], [$entry => $res]);
}

if (!function_exists('get_entry_config'))
{
    function get_entry_config($entry)
    {
        return ci()->Entry_model->get_entry_config($entry);
    }
}

if (!function_exists('setup_entry_model'))
{
    function setup_entry_model($entry)
    {
        // Get entry yaml
        if(!isset(config_item('entries')[$entry])) show_error("Setup entry '$entry' failed, entry not found");
        $entryConf = config_item('entries')[$entry] ?? false;

        // Create main entry model
        $modelName = ucfirst($entry).'Model';
        if(!isset(ci()->$modelName)){
            eval("class $modelName extends MY_Model {};");

            // Register model object to CI global models
            ci()->load->registerModel($modelName, new $modelName($entryConf['table'], $entryConf['fields']));
            ci()->$modelName->protected = ['id'];
        }

        // Instantiate entry relation model object first
        $Relation_model = [];
        foreach ($entryConf['fields'] as $field => $options) 
        {
            if(isset($options['relation']['entry']))
            {
                $relEntry = $options['relation']['entry'];

                $modelName = ucfirst($relEntry).'Model';

                if($options['relation']['model'] ?? '') {
                    ci()->load->model($options['relation']['model_path']);
                    $modelName = $options['relation']['model'];
                }
                
                // Get foreign entry
                elseif($foreign_entry = config_item('entries')[$relEntry] ?? '')
                {
                    // Create children model class on the fly
                    if(! class_exists($modelName, false))
                    {
                        eval("class $modelName extends MY_Model {};");

                        // Register model object to CI global models
                        ci()->load->registerModel($modelName, new $modelName($foreign_entry['table'], $foreign_entry['fields']));
                    }
                
                } else {
                   show_error("Entry $relEntry or native model for $modelName not found.");
                }

                ci()->$modelName->protected = ['id'];

                // Track model relation
                $Relation_model[$options['relation']['alias'] ?? $relEntry] = [
                    'type' => 'has_one',
                    'modelName' => $modelName,
                    'format' => [
                        'foreign_model' => $modelName, 
                        'foreign_table' => $foreign_entry['table'] ?? ci()->$modelName->table,
                        'foreign_key' => $options['relation']['foreign_key'] ?? 'id', 
                        'local_key' => $options['relation']['local_key'] ?? $field,
                        'fields' => $options['relation']['fields'] ?? 'fields:*',
                        'filter' => $options['relation']['filter'] ?? 'filter:null',
                    ]
                ];
            }
        }

        // Instantiate model object
        $Entrydata_model = new MY_Model($entryConf['table'], $entryConf['fields'], $Relation_model);
        $Entrydata_model->protected = ['id'];
        $Entrydata_model->id_type = $entryConf['id_type'] ?? 'increment';
        $Entrydata_model->uuid_fields = $entryConf['uuid_fields'] ?? [];
        $Entrydata_model->soft_deletes = $entryConf['soft_deletes'] ?? false;
        $Entrydata_model->show_on_table = $entryConf['show_on_table'] ?? [];

        if($Entrydata_model->id_type == 'uuid')
            $Entrydata_model->fillable[] = $Entrydata_model->primary_key;

        $Entrydata_model->has_many = $entryConf['has_many'] ?? [];
        $Entrydata_model->has_many_pivot = $entryConf['has_many_pivot'] ?? [];

        $Entrydata_model->timestamps = $entryConf['timestamps'] ?? true;
        $Entrydata_model->entryConf = $entryConf;

        return $Entrydata_model;
    }

}

// DEPRECATED
if (!function_exists('create_entry_model'))
{
    function create_entry_model($entry)
    {
        // Get entry yaml
        if(!isset(config_item('entries')[$entry])) show_error("Creating entry '$entry' failed, entry not found");
        $entryConf = config_item('entries')[$entry] ?? false;

        $modelName = ucfirst($entry).'Model';
        eval("class $modelName extends MY_Model {};");

        // Register model object to CI global models
        ci()->load->registerModel($modelName, new $modelName($entryConf['table'], $entryConf['fields']));
        ci()->$modelName->protected = ['id'];
    }
}

if (!function_exists('table_explode'))
{
    function table_explode($row_separator, $column_separator, $source)
    {
        $rows = explode($row_separator, $source);
        foreach ($rows as &$row) {
            $row = explode($column_separator, $row);
        }
        return $rows;
    }

}

if (!function_exists('embed_entry_script'))
{
    function embed_entry_script()
    {
        return ci()->load->view('entry/form-script');
    }

}

if (!function_exists('embed_entry_style'))
{
    function embed_entry_style()
    {
        $CI = &get_instance();
        return $CI->load->view('entry/form-style');
    }

}
