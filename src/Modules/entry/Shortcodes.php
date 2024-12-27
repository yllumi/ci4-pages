<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *	Entry Shortcode
 *	
 *  Theme api for Entry feature
 */
class EntryShortcode extends Shortcode {

	public function form()
	{
		// Retrieve extension attributes
        $entry = $this->getAttribute('entry', '');
        $button_label = $this->getAttribute('button_label', 'Send');
        $button_class = $this->getAttribute('button_class', 'btn btn-success');
        $success_message = $this->getAttribute('success_message', '<div class="alert alert-success">Done!</div>');
        
        $this->session->set_userdata('success_message', $success_message);

        $fields = $this->config->config['entries'][$entry]['fields'];
        
        $output  = form_open(site_url('entry/insert'), ['id' => 'form' . $entry, 'method' => 'post', 'enctype' => 'multipart/form-data']);
        $output .= $this->session->flashdata('message');
        $output .= '<input type="hidden" name="entry" value="'. $entry .'" />';
        
        foreach($fields as $field => $fieldOptions)
        {
            if(!isset($fieldOptions['dataform']) || isset($fieldOptions['dataform']) && $fieldOptions['dataform'] != false) {
                $output .= '<div class="form-group">';
                $output .= '<label>'. $fieldOptions['label'] .'</label>';
                $output .= generate_input($fieldOptions);
                $output .= '</div>';
            }
        }

        $output .= '<button type="submit" class="'. $button_class .'">'. $button_label .'</button>';
        $output .= '</form>';

		return $this->output($output);
    }

    public function form_elements()
    {
        // Retrieve extension attributes
        $entry = $this->getAttribute('entry', '');
        $redirect = $this->getAttribute('redirect', '');
        $data = $this->getAttribute('data') ?? [];
        $type = $data ? 'update' : 'insert';
        $success_message = $this->getAttribute('success_message', '<div class="alert alert-success">Done!</div>');
        
        $this->session->set_userdata('success_message', $success_message);
        
        $form_open  = form_open(site_url('entry/'.$type), ['id' => 'form' . $entry, 'method' => 'post', 'enctype' => 'multipart/form-data']);
        $form_open .= '<input type="hidden" name="entry" value="'.$entry.'">';
        $form_open .= '<input type="hidden" name="redirect" value="'.$redirect.'">';

        if($data['id'] ?? '')
            $form_open .= '<input type="hidden" name="id" value="'.$data['id'].'">';

        $form_close = form_close();

        $fields = $this->config->config['entries'][$entry]['fields'];

        // Load all required models
        if($this->config->config['entries'][$entry]['has_one'] ?? '')
            foreach ($this->config->config['entries'][$entry]['has_one'] as $has_one => $relOptions) {
                if(isset($relOptions['model_path']))
                    ci()->load->model($relOptions['model_path']);
            }


        foreach($fields as $field => &$fieldOptions)
        {
            if(!isset($fieldOptions['dataform']) || isset($fieldOptions['dataform']) && $fieldOptions['dataform'] != false) {
                $element = '<div class="form-group">';
                $element .= generate_input($fieldOptions, (!empty($data[$field]) ? $data[$field] : null));
                $element .= '</div>';

                $fieldOptions['element'] = $element;
            }
        }

        $output = compact('entry', 'success_message', 'form_open', 'form_close', 'fields');
        return $this->output($output);
    }
    
    public function loop()
    {
		// Retrieve extension attributes
        $entry = $this->getAttribute('entry', '');
        $limit = $this->getAttribute('limit', null);
        $order = $this->getAttribute('order', null);
        $entryConf = $this->config->config['entries'][$entry] ?? '';
        if(!$entryConf) return null;
        
        // Business Process.
        if($this->Entry_model->table_exist($entryConf['table']))
        {
            $data['results'] = $this->Entry_model->get_all($entryConf['table'], $limit, $order);
            
            return $this->output($data);
        }
        
        return null;
    }

    public function detail()
    {
        $entry = $this->getAttribute('entry');
        $entryConf = $this->config->config['entries'][$entry] ?? '';
        if(!$entryConf) return null;

        // Business Process.
        if($this->Entry_model->table_exist($entryConf['table']))
        {
            $where = $this->getAttribute('where', '');
            if($where) parse_str($where, $arr);
            
            $Entrydata_model = setup_entry_model($entry);
            
            if(!empty($Entrydata_model->has_one))
                foreach ($Entrydata_model->has_one as $relation_entry => $opt) {
                    $withFunction = 'with_'.$relation_entry;
                    $Entrydata_model->$withFunction();
                }

            $data = $Entrydata_model->where($arr)->get();

            return $this->output($data);
        }
        
        return null;
    }

    public function get()
    {
        $entry = $this->getAttribute('entry');
        $entryConf = $this->config->config['entries'][$entry] ?? '';
        if($entryConf) 
            return $this->output($entryConf);
        return null;
    }
}
