<?php
$choosen = $value;
foreach ($config['options'] as $key => $val) {
    if ($choosen == $val)
        $attribute = 'checked';
    else
        $attribute = '';
?>
    <div class="form-check">
        <input name="<?php echo $config['field']; ?>" class="form-check-input" type="radio" id="<?php echo $key; ?>" value="<?php echo $val; ?>" <?php echo $attribute; ?>>
        <label class="form-check-label" for="<?php echo $key; ?>">
            <?php echo $val; ?>
        </label>
    </div>

<?php
}
?>
<div class="form-check">
    <input name="<?php echo $config['field']; ?>" class="form-check-input" type="radio" id="<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other" name="<?php echo $config['field']; ?>" <?php echo $attribute; ?> <?= in_array($value, array_keys($config['options'])) ? '' : 'checked'; ?> value="<?= $value; ?>">
    <label class="form-check-label" for="<?php echo $config['field']; ?>_other" style="min-width: 300px;">
        <input type="text" placeholder="<?= $config['placeholder'] ?? 'lainnya..'; ?>" class="form-control" id="input_<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other" autocomplete="mati" value="<?= in_array($value, array_keys($config['options'])) ? '' : $value; ?>">
    </label>
</div>
<script>
    $(function() {
        $('#input_<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other').on('focus', function() {
            $('#<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other').prop('checked', true);
        });
        $('#input_<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other').on('keyup', function() {
            $('#<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>_other').val($(this).val());
        });
    });
</script>