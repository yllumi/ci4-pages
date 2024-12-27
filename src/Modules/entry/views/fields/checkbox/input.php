<?php
if(is_array($value))
    $chosen = $value;
else
    $chosen = json_decode($value, true);

if (empty($chosen)) $chosen = [];

foreach ($config['options'] as $key => $val)
{
    if (in_array($key, array_values($chosen)))
        $attribute = 'checked';
    else
        $attribute = '';
    ?>

    <div class="form-check">
        <input name="<?php echo $config['field'] ;?>[]" class="form-check-input" type="checkbox" value="<?php echo $val;?>" id="<?php echo $key;?>" <?php echo $attribute;?>>
        <label class="form-check-label" for="<?php echo $key;?>">
            <?php echo $val;?>
        </label>
    </div>

    <?php
}
?>