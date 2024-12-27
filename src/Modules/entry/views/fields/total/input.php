<?php
	$attrs = '';
	if(isset($config['attr']))
		foreach ($config['attr'] as $key => $val) {
			$attrs .= $key.'="'.$val.'" ';
		}
?>
<div class="form-inline">
	<button id="sum<?= $config['field'];?>" type="button" class="btn btn-info py-2 mt-1 me-2">&sum;</button>
	<input id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" type="number" name="<?php echo $config['field'];?>" value="<?php echo $value; ?>" class="form-control" <?= $attrs; ?> data-caption="<?= $config['label'];?>" />
</div>
<script>
$(function(){
	$('#sum<?= $config['field'];?>').on('click', function(){
		let total = 0;
		<?php foreach($config['sum'] as $sumField): ?>
		total += parseInt($('#<?= $sumField; ?>').val())
		<?php endforeach; ?>
		$('#<?= $config['field'];?>').val(total);
	})
})
</script>