<?php

use App\core\Backend_Controller;
use Symfony\Component\Yaml\Yaml;

class Porter extends Backend_Controller 
{
    public function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, "id_ID.utf8", "id_ID", "id");
        date_default_timezone_set("Asia/Jakarta");
    }

    public function import($slug = null)
    {
        $ImporterModel = setup_entry_model('importer');
        $importer = $ImporterModel->where('slug',$slug)->get();
        $importer['schema_array'] = Yaml::parse($importer['schema']);

        if($postdata = $this->input->post(null, true))
        {
            $data['slug'] = $slug;
            $data['file'] = '.' . str_replace(base_url(), '', $postdata['csv_file']);
            $data['header'] = [];
            $data['data'] = [];
            $row = 1;
            if (($handle = fopen($data['file'], "r")) !== FALSE) {
                while (($rowdata = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if($row == 1)
                        $data['header'] = $rowdata;
                    else
                        $data['data'][] = $rowdata;
                    $row++;
                }
                fclose($handle);
            }
            array_shift($data['data']);
        }        

        $data['identifier'] = $importer['schema_array']['identifier'];
        $data['page_title'] = 'Import Data '.$importer['title'];
        $this->view('admin/import', $data);
    }

    public function processImport($slug = null)
    {
        $postdata = $this->input->post(null, true);
        
        $ImporterModel = setup_entry_model('importer');
        $importer = $ImporterModel->where('slug',$slug)->get();
        $schema_array = Yaml::parse($importer['schema']);

        $header = [];
        $csvData = [];
        $row = 1;
        if (($handle = fopen($postdata['file'], "r")) !== FALSE) {
            while (($rowdata = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($row == 1)
                    $header = $rowdata;
                else
                    $csvData[] = array_combine($header, $rowdata);
                $row++;
            }
            fclose($handle);
        }
        array_shift($csvData);
        
        // Begin inserting

        if($schema_array['class']) 
            $ImporterAction = new $schema_array['class'];

        // Loop csv data
        $inserted = 0;
        $updated = 0;
        foreach($csvData as $rowData)
        {
            // For each csv row, loop the table target
            foreach($schema_array['target'] as $table => $schemaFields)
            {
                $prepare = [];

                // For each table, loop the columns 
                foreach($schemaFields as $schemaField => $schemaFieldConfig)
                {
                    // Check if field config bring the function to process the data
                    $schemaFieldConfigArray = explode('|', trim($schemaFieldConfig));
                    $dataField = $schemaFieldConfigArray[0];
                    $dataFunction = $schemaFieldConfigArray[1] ?? null;

                    // Prepare all value from csv row for inserting
                    if(!empty($rowData[$dataField] ?? ''))
                    {
                        $prepare[strtolower($schemaField)] = isset($dataFunction) && isset($ImporterAction) 
                                                    ? $ImporterAction->{$dataFunction}($rowData[$dataField] ?? '')
                                                    : $rowData[$dataField] ?? '';
                    }
                }

                $identifierColumn = $schema_array['identifier'][$table];
                if(! is_array($identifierColumn)) $identifierColumn = [$identifierColumn];
                foreach ($identifierColumn as $col) {
                    ci()->db->where($col, $prepare[$col]);
                }
                if($exists = ci()->db->get($table)->row_array())
                {
                    ci()->db->where('id',$exists['id'])->update($table, $prepare);
                    $rowData[$table.'.id'] = $exists['id'];
                    $updated++;
                } else {
                    ci()->db->insert($table, $prepare);
                    $rowData[$table.'.id'] = ci()->db->insert_id();
                    $inserted++;
                }
            }
        }

        $this->session->set_flashdata('message', '<div class="alert alert-info">'.$inserted.' new data and '.$updated.' updated data.</div>');
        redirect(getenv('HTTP_REFERER'));
    }

    public function preview_export($id)
    {
        $ExporterModel = setup_entry_model('exporter');
        $export = $ExporterModel->get($id);

        // Get header and total
        $result = ci()->db->query($export['query']);
        $header = $result;
        $data['header'] = array_keys($header->row_array());
        $total = $result;
        $data['total'] = $total->num_rows();
        
        // Get data result
        $data['result'] = ci()->db->query($export['query']);
        $data['export'] = $export;
        $data['page_title'] = 'Preview Export - '.$export['title'];
        $this->view('admin/preview_export', $data);
    }

}
