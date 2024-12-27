<?php
if (isset($config['relation'])) {
    $relEntry = $config['relation']['entry'];
    if (isset($config['relation']['model'])) {
        $modelName = $config['relation']['model'];
        if (!isset($this->{$config['relation']['model']}))
            $this->load->model($config['relation']['model_path']);
    } else {
        $modelName = ucfirst($relEntry);
        $modelName .= isset($this->{$modelName . '_model'}) ? '_model' : 'Model';
    }

    $caption = is_array($config['relation']['caption'])
        ? $config['relation']['caption'][0]
        : $config['relation']['caption'];
    $config['options'] = $this->$modelName->as_dropdown($caption)->getAll();
}

if (empty($config['options']))
    $config['options'] = ["" => "Semua"];
else
    $config['options'] = ["" => "Semua"] + $config['options'];
?>

<?= form_dropdown("filter[" . $config['field'] . "]", $config['options'], $this->input->get("filter[{$config['field']}]", true), 'class="form-control form-control-sm" placeholder="filter by ' . $config['field'] . '"'); ?>

