<?php

use Symfony\Component\Yaml\Yaml;

/**
 * Entry
 *
 * The business model for entry.
 * entry equivalent to Stream in Pyro CMS / Custom Post in Wordpress
 *
 * @author Oriza
 */

class Entry_model extends CI_Model
{
    public $entry_config_path;
	public $table;
    public $fields;
    protected $entries = [];

    public $postmetadata;
    
	public function __construct($params = [])
	{
		parent::__construct($params);

        $this->load->config('entry/config');

    }

    public function get_all($table, $limit = 5, $order = 0)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->limit($limit, $order);

        return $this->db->get()->result_array();
    }

    public function get_all_with_relation($table, $relation, $id)
    {
        $relation_table =  $table . '_' . $relation;

        $this->db->select('*,' . $relation_table . '.id as relation_id');
        $this->db->from( $table);
        $this->db->join($relation_table,  $table . '.id = ' . $relation_table . '.' . $table . '_id');
        $this->db->join( $relation,  $relation . '.id =' . $relation_table . '.' . $relation . '_id');
        $this->db->where( $table . '.id', $id);

        return $this->db->get()->result_array();
    }

    public function get_active_entries()
    {
        return $this->entries;
    }

    public function get_show_on_table($entry)
    {
        if(isset($this->entries[$entry]['show_on_table']))
            return $this->entries[$entry]['show_on_table'];

        return array_keys($this->entries[$entry]['fields']);
    }

    public function get_fields($entry)
    {
        $entries = $this->get_active_entries();

        if (!isset($entries[$entry]['fields']))
            return null;

        // Inject several default index for avoid error.
        foreach($entries[$entry]['fields'] as $field => $value)
        {
            if (!isset($value['referrer']))
                $entries[$entry]['fields'][$field]['referrer'] = null;
        }

        return $entries[$entry]['fields'];
    }

    public function getEntryName($entry)
    {
        $entries = $this->get_active_entries();

        if (!isset($entries[$entry]['name']))
            return null;

        return $entries[$entry]['name'];
    }

    public function get_dropdown_options($entry, $field)
    {
        $entries = $this->get_active_entries();

        // Jika ada dari relasi, maka ambil nya dari sana
        if (isset($entries[$entry]['fields'][$field]['relation']))
        {
            $results = $this->get_all($entries[$entry]['fields'][$field]['relation']['entry']);

            return ['relation' => $entries[$entry]['fields'][$field]['relation'], 'results' => $results];
        }

        // Jika tidak ambil dari options array.
        if (isset($entries[$entry]['fields'][$field]['options']))
            return $entries[$entry]['fields'][$field]['options'];

        return null;
    }

    public function search($table, $field, $keyword)
    {
        $this->db->select('*');
        $this->db->from( strtolower($table));
        $this->db->like($field, $keyword);

        return $this->db->get()->result_array();
    }

    public function get_detail($table, $id)
    {
        $this->db->select('*');
        $this->db->from( strtolower($table));
        $this->db->where('id', $id);
        $this->db->where('deleted_at', null);

        return $this->db->get()->row_array();
    }

    public function get_detail_where($table, $where)
    {
        $this->db->from(strtolower($table));
        $this->db->where($where);

        return $this->db->get()->row_array();
    }

    public function get_field_content($table, $id, $field_name)
    {
        $this->db->select($field_name . ' as field');
        $this->db->from( strtolower($table));
        $this->db->where('id', $id);

        $result = $this->db->get()->row_array();

        if (!empty($result))
            return $result['field'];
        
        return null;
    }

    /** 
     * Get relation
     * 
     * Get many to many relation table by entry
     * 
     * @param string $entry
     * @return string
     */
    public function get_relation($entry)
    {
        if(isset($this->entries[$entry]['relation']) && !empty($this->entries[$entry]['relation']))
            return $this->entries[$entry]['relation'];
        
        return '';
    }

    /** 
     * Get entry db
     * 
     * Get get entry db yaml from files.
     * 
     * @return string
     */
    public function get_entry_config($entry)
    {
        if(config_item('entries')[$entry] ?? '')
            return config_item('entries')[$entry];
        else
            return false;
    }

    /** 
     * Update DB
     * 
     * Update YAML DB on files.
     * 
     * @return string
     */
    public function update_db($oldname, $name, $content)
    {
        // Renaming means delete old file
        if(!empty($oldname) && $oldname != $name){
            unlink($this->entry_config_path.$oldname.'/schema.yml');
        }

        write_file($this->entry_config_path.$name.'schema.yml', $content);
        return true;
    }

    /**
     * Sync
     *
     * Generate table on DB by Entries Setting
     *
     * @return bool
     */
    public function sync($entry, $yaml)
    {
        // Init
        $param = [];

        // Load forge
        $this->load->dbforge();

        // Define table name
        $table = strtolower($yaml['table']);

        // Define owner id field
        if($yaml['set_owner'] ?? false)
            $yaml['fields']['owner'] = [
                'field' => 'owner',
                'label' => 'Owner',
                'form' => 'owner',
                'type' => 'int',
                'null' => true
            ];

        // Define default fields
        $yaml['fields']['created_at'] = [
            'label' => 'Created at',
            'type' => 'timestamp default CURRENT_TIMESTAMP',
            'null' => true
        ];
        $yaml['fields']['updated_at'] = [
            'label' => 'Updated at',
            'type' => 'timestamp',
            'null' => true
        ];
        $yaml['fields']['deleted_at'] = [
            'label' => 'Deleted at',
            'type' => 'timestamp',
            'null' => true
        ];

        if ($this->db->table_exists($table))
        {
            // Update field table
            foreach($yaml['fields'] as $field => $fieldOptions)
            {
                if(! isset($fieldOptions['type']))
                    $fieldOptions['type'] = $this->_define_field_spec($fieldOptions);

                // Set nullable for every column
                $fieldOptions['null'] = true;

                if (!$this->db->field_exists($field, $table))
                {
                    $this->dbforge->add_column($table, [
                        $field => $fieldOptions
                    ]);
                }
            }

        }
        else
        {
            // Install primary key
            $param['id'] = ['type' => 'int(11) unsigned primary key auto_increment'];

            // Building fields for table forge.
            foreach($yaml['fields'] as $field => $fieldOptions)
            {
                if(! isset($fieldOptions['type']))
                    $fieldOptions['type'] = $this->_define_field_spec($fieldOptions);

                // Set nullable for every column
                $fieldOptions['null'] = true;

                $param[$field] = $fieldOptions;
            }

            $this->dbforge->add_field($param);
            $this->dbforge->create_table($table);
        }

        return true;
    }

    function insert($entry, $postdata = [])
    {
        $entryConf = config_item('entries')[$entry] ?? false;

        $Entrydata_model = setup_entry_model($entry);

        // Add this if you want to override post data
        $postdata = $this->input->post(null, true);
        $postdata = $this->_jsonDecode($postdata);
        $postdata = $this->_separateMultipleData($Entrydata_model->fields, $postdata);

        // Trigger before insert
        $postdata = $this->_callback_before_insert($entry, $postdata);
        
        $Entrydata_model->set_form_data($postdata);
        $Entrydata_model->validate();
        $result = $Entrydata_model->insert();

        if($result){
            if($this->postmetadata ?? ''){
                foreach ($this->postmetadata as $metafield => $values) {
                    $metadata['table'] = $Entrydata_model->fields[$metafield]['relation']['pivot_table'];
                    $metadata['foreign_key'] = $Entrydata_model->fields[$metafield]['relation']['pivot_foreign_key'];
                    $metadata['foreign_data'] = $values;
                    $metadata['local_key'] = $Entrydata_model->fields[$metafield]['relation']['pivot_local_key'];
                    $metadata['local_id'] = $result['id'];
                    $this->_insertPivotData($metadata);
                }
            }

            // Trigger after_insert
            $result = $this->_callback_after_insert($entry, $result);

            return $result;
        }

        return false;
    }

    public function update($entry, $id, $postdata = null)
    {

        $entryConf = config_item('entries')[$entry] ?? false;
        
        $Entrydata_model = setup_entry_model($entry);
        if(isset($entryConf['soft_deletes']))
        $Entrydata_model->soft_deletes = $entryConf['soft_deletes'];
        
        // Add this if you want to override post data
        $postdata = $postdata ?? $this->input->post(null, true);
        $postdata = $this->_jsonDecode($postdata);
        $postdata = $this->_separateMultipleData($Entrydata_model->fields, $postdata);
        
        // Trigger before update
        $postdata['id'] = $id;
        $postdata = $this->_callback_before_update($entry, $postdata);

        $Entrydata_model->set_form_data($postdata);
        $Entrydata_model->validate('update');

        // Filter by uuid
        $where = ['id' => $id];
        if($Entrydata_model->id_type == 'uuid')
            $where = ['id' => uuid2bin($id)];

        $affected = $Entrydata_model->where($where)->update();
        
        if($this->postmetadata ?? ''){
            foreach ($this->postmetadata as $metafield => $values) {
                $metadata['table'] = $Entrydata_model->fields[$metafield]['relation']['pivot_table'];
                $metadata['foreign_key'] = $Entrydata_model->fields[$metafield]['relation']['pivot_foreign_key'];
                $metadata['foreign_data'] = $values;
                $metadata['local_key'] = $Entrydata_model->fields[$metafield]['relation']['pivot_local_key'];
                $metadata['local_id'] = $id;
                $this->_insertPivotData($metadata);
            }
        }
        
        // Trigger after update
        $postdata = $this->_callback_after_update($entry, $postdata, $affected);

        return $affected;
    }

    private function _jsonDecode($data)
    {
        foreach ($data as $field => &$value) {
            if(is_string($value)){
                $array = json_decode($value, true);
                if(is_array($array))
                    $value = $array;
            }
        }

        return $data;
    }

    private function _separateMultipleData($entryFields, $data)
    {
        foreach ($data as $field => &$value) {
            if(is_array($value)){
                if(isset($entryFields[$field]['relation']['pivot_table'])) {
                    $this->postmetadata[$field] = $value;
                    unset($data[$field]);
                } else {
                    $data[$field] = json_encode($data[$field]);
                }
            }
        }

        return $data;
    }

    private function _insertPivotData($metadata)
    {
        // first delete previous metadata
        $this->db->where($metadata['local_key'], $metadata['local_id'])->delete($metadata['table']);

        // Insert batch
        $data = [];
        foreach($metadata['foreign_data'] as $value){
            $data[] = [
                $metadata['local_key'] => $metadata['local_id'],
                $metadata['foreign_key'] => $value
            ];
        }

        $this->db->insert_batch($metadata['table'], $data);
    }

    /**
     * Is Related
     *
     * Check current entry is many to many related or no.
     *
     * @param string $entry
     * @return bool
     */
    public function is_related($entry)
    {
        if(isset($this->entries[$entry]['relation']) && !empty($this->entries[$entry]['relation']))
            return true;

        return false;
    }

    public function remove_relation($entry, $relation, $relation_id)
    {
        $relation_table =  $entry . '_' . $relation;

        $this->db->delete($relation_table, ['id' => $relation_id]);

        return true;
    }

    private function _parse($yml)
    {
        return Yaml::parse($yml);
    }

    private function _define_field_spec($fieldOptions)
    {
        if($fieldOptions['relation'] ?? '')
            $fieldOptions['form'] = 'numeric';

        switch($fieldOptions['form'] ?? 'text') {
            case 'switch':
                $spec = 'int(1)';
                break;
            case 'float':
                $spec = 'float';
                break;
            case 'number':
            case 'owner':
            case 'currency':
                $spec = 'int('. ($fieldOptions['constraint'] ?? 11) .')';
                break;
            case 'numeric':
            case 'textarea':
            case 'markdown':
            case 'signature':
            case 'rte':
            case 'code':
                $spec = 'text';
                break;
            case 'time':
                $spec = 'time';
                break;
            case 'date':
                $spec = 'date';
                break;
            case 'datetime':
                $spec = 'datetime';
                break;
            default:
                $spec = 'varchar('. ($fieldOptions['constraint'] ?? 255) .')';
        }

        return $spec;
    }

    public function table_exist($entry)
    {
        $tablename = $entry;
        return $this->db->table_exists($tablename);
    }


    // Callback functions

    private function _callback_before_insert($entry, $data)
    {
        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $data;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'beforeInsert'))
            return $actionClass->beforeInsert($data);

        return $data;
    }
    private function _callback_after_insert($entry, $result)
    {
        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $result;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'afterInsert'))
            return $actionClass->afterInsert($result);

        return $result;
    }
    private function _callback_before_update($entry, $data)
    {
        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $data;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'beforeUpdate'))
            return $actionClass->beforeUpdate($data);

        return $data;
    }
    private function _callback_after_update($entry, $data, $affected)
    {
        if(! $affected) return false;
        
        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $affected;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'afterUpdate'))
            return $actionClass->afterUpdate($data, $affected);

        return $data;
    }

    public function _callback_before_delete($entry, $id)
    {
        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $id;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'beforeDelete'))
            return $actionClass->beforeDelete($id);
    }

    public function _callback_after_delete($entry, $id, $affected)
    {
        if(! $affected) return false;

        $EntryActionFile = ucfirst($entry).'EntryAction';
        if(! file_exists(config_item('entries')[$entry]['path'].'/Action.php'))
            return $affected;

        include_once(config_item('entries')[$entry]['path'].'/Action.php');
        $actionClass = new $EntryActionFile();

        if(method_exists($actionClass, 'afterDelete'))
            return $actionClass->afterDelete($id);
    }

}
