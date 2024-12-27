<textarea 
    id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" 
    class="form-control" 
    rows="<?= $config['rows'] ?? 5 ?>" 
    name="<?php echo $config['field'];?>" 
    placeholder="<?= $config['placeholder'] ?? '';?>" <?= $config['attr'] ?? '';?> 
    data-caption="<?= $config['label'];?>">
<?php
    $valueArray = json_decode($value, true); 
    echo $valueArray ? json_encode($valueArray, JSON_PRETTY_PRINT) : '';
?>
</textarea>
