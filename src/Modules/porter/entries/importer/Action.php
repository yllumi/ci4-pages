<?php

// function topaction_taskname() for entry action
// function rowaction_actionname() for action per data row
use Symfony\Component\Yaml\Yaml;

class ImporterEntryAction {

  function rowaction_upload($EntryModel, $id)
  {
    $data = $EntryModel->get($id);
    redirect('admin/porter/import/'.$data['slug']);
  }

  function rowaction_template($EntryModel, $id)
  {
    $data = $EntryModel->get($id);
    $data['schema_array'] = Yaml::parse($data['schema']);
    
    $rows[] = array_keys($data['schema_array']['fields']);
    $rows[] = array_values($data['schema_array']['fields']);

    $filename = SITEPATH.'resources/csv/template_import_'.$data['slug'].'.csv';
    $fp = fopen($filename, 'w');
    foreach ($rows as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
    
    ci()->load->helper('download');
    force_download($filename, null);
    redirect(getenv('HTTP_REFERER'));
  }

}