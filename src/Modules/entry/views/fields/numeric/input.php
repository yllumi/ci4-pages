<?php
$attrs = '';
if (isset($config['attr']))
	foreach ($config['attr'] as $key => $val) {
		$attrs .= $key . '="' . $val . '" ';
	}
?>
<input id="<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>" type="number" name="<?php echo $config['field']; ?>" value="<?php echo $value; ?>" class="form-control" <?= $attrs; ?> data-caption="<?= $config['label']; ?>" <?= strpos($config['rules'] ?? '', 'required') !== false ? 'required' : ''; ?> autocomplete="off" />