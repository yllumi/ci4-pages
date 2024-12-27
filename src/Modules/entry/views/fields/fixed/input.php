<?php
$idname = str_replace(['[', ']'], ['__', ''], $config['field']);

if ($config['relation'] ?? '')
{
    $relEntry = $config['relation']['entry'];
    if (isset($config['relation']['model'])) {
        $modelName = $config['relation']['model'];
        if (!isset($this->{$config['relation']['model']}))
            $this->load->model($config['relation']['model_path']);
    } else {
        $modelName = ucfirst($relEntry) . 'Model';
        $this->$modelName = setup_entry_model($relEntry);
    }

    $caption = $config['relation']['dropdown_caption'] ?? $config['relation']['caption'];
    
    if (! isset($config['relation']['filter_by']))
        show_error('Form type fixed need property relation.filter_by to be set.');
    if (! isset($_GET['filter']))
        show_error('Form type fixed need query string \'filter\' to be set.');

    foreach ($config['relation']['filter_by'] as $localField => $foreignField)
    {
        $filterValue = ci()->input->get('filter')[$foreignField] ?? '';
        if ($filterValue == "null")
        ci()->db->where($localField . ' is null', null, false);
        else
        $this->$modelName->where($localField, $filterValue);
    }
        
    $data = $this->$modelName->get();
    $value = $filterValue;
    $valueLabel = $data[$caption];
}
?>

<?php if (($config['hide_form'] ?? true) == false) : ?>
    <input type="text" disabled value="<?= $valueLabel ?? $_GET['filter'][$config['field']]; ?>" class="form-control" />
<?php endif; ?>
<input type="hidden" id="<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>" name="<?= $config['field']; ?>" value="<?= !empty($value) ? $value : $_GET['filter'][$config['field']]; ?>" data-caption="<?= $config['label']; ?>" />