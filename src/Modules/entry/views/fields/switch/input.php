<label class="align-middle pe-2"><?= $config['options'][0]; ?></label>
<label class="align-middle switch" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>">
		<input type="checkbox" <?= $value == '1' ? 'checked':''; ?>>
		<span class="slider round d-inline-block"></span>
		<input type="hidden" name="<?= $config['field'];?>" value="<?= $value; ?>" data-caption="<?= $config['label'];?>">
</label>
<label class="align-middle ps-2"><?= $config['options'][1]; ?></label>

<script>
	$(function(){
		let swParent = $('#<?= str_replace(['[',']'], ['__',''], $config['field']);?>');
		swParent.children('input[type=checkbox]').on('change', function(){
			let checked = $(this).prop('checked');
			swParent.children('input[type=hidden]').val(Number(checked));
		})
	})
</script>
