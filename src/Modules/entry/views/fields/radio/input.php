<?php
$choosen = $value;
foreach ($config['options'] as $key => $value) :
    $attribute = '';
    if ($choosen == $value || $choosen == $key)
        $attribute .= 'checked ';
    if (strpos(($config['rules'] ?? ''), 'required') !== false)
        $attribute .= 'required ';
    if (($config['disabled'] ?? false) == true)
        $attribute .= 'disabled ';
?>

    <div class="form-check <?= ($config['inline'] ?? false) == true ? 'form-check-inline' : ''; ?>">
        <input name="<?= $config['field']; ?>" class="form-check-input" type="radio" id="<?= $config['field'] . '_' . $key; ?>" value="<?= $key; ?>" <?= $attribute; ?>>
        <label class="form-check-label" for="<?= $config['field'] . '_' . $key; ?>">
            <?= $value; ?>
        </label>
    </div>

<?php endforeach; ?>