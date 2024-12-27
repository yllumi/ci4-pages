<?php

// function topaction_taskname() for entry action
// function rowaction_actionname() for action per data row

class ExporterEntryAction {
    
    private $disallowed_query = [
        'CREATE ',
        'ALTER ',
        'DROP ',
        'TRUNCATE ',
        'INSERT INTO ',
        'INTO ',
        'UPDATE ',
        'DELETE FROM ',
    ];
    
    function rowaction_preview($EntryModel, $id)
    {
        redirect('admin/porter/preview_export/'.$id);
    }
    
    function rowaction_download($EntryModel, $id)
    {
        $data = $EntryModel->get($id);
        if(empty($data['query'])){
            ci()->session->set_flashdata('message', '<div class="alert alert-warning">Query belum diset</div>');
        } else {
            $result = ci()->db->query($data['query']);
            
            ci()->load->dbutil();
            $csv = ci()->dbutil->csv_from_result($result);
            
            ci()->load->helper('download');
            force_download($data['slug'].'-'.date('Y-m-d-H-i').'.csv', $csv);
        }
        
        redirect(getenv('HTTP_REFERER'));
    }
    
    function beforeInsert($data)
    {
        foreach($this->disallowed_query as $disallowed)
        {
            if(stripos($data['query'], $disallowed) !== FALSE){
                ci()->session->set_flashdata('message', '<div class="alert alert-danger">Only SELECT query allowed.</div>');
                redirect(getenv('HTTP_REFERER'));
            }
        }
    }
    
    function beforeUpdate($data)
    {
        foreach($this->disallowed_query as $disallowed)
        {
            if(stripos($data['query'], $disallowed) !== FALSE){
                ci()->session->set_flashdata('message', '<div class="alert alert-danger">Only SELECT query allowed.</div>');
                redirect(getenv('HTTP_REFERER'));
            }
        }
    }
}