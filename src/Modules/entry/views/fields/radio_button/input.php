<div class="btn-group btn-group-toggle" data-bs-toggle="buttons">
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

        <input type="radio" class="btn-check form-check-input" name="<?= $config['field']; ?>" id="<?= $config['field'] . '_' . $key; ?>" autocomplete="off" value="<?= $key; ?>" <?= $attribute; ?>>
        <label for="<?= $config['field'] . '_' . $key; ?>" class="btn btn-outline-primary value_<?= $key; ?>" <?= ($config['disabled'] ?? false) == true ? 'disabled' : ''; ?>>
            <?= $value; ?>
        </label>

    <?php endforeach; ?>
</div>