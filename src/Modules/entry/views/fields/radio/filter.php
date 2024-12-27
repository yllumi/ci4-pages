<?php 
if (isset($config['relation'])){
	$relEntry = $config['relation']['entry'];
    $modelName = ucfirst($relEntry).'Model';
    $caption = $config['relation']['caption'];
    $config['options'] = $this->$modelName->as_dropdown($caption)->getAll();
}

if(empty($config['options']))
	$config['options'] = [""=>"Semua"];
else
	$config['options'] = [""=>"Semua"] + $config['options'];
?>

<?= form_dropdown("filter[".$config['field']."]", $config['options'], $this->input->get("filter[{$config['field']}]", true), 'class="form-control form-control-sm" placeholder="filter by '.$config['field'].'"'); ?>

