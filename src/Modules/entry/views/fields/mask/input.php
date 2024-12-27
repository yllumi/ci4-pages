<?php $idname = str_replace(['[',']'], ['__',''], $config['field']); ?>
<div class="input-group">
	<input type="password" 
	id="<?= $idname; ?>" 
	name="<?= $config['field'];?>"
	placeholder="<?= $config['placeholder'] ?? '';?>" 
	value="<?= $value;?>" 
	class="form-control" 
	data-caption="<?= $config['label'];?>"/>
	
	<div class="input-group-append">
	   <button class="btn mb-0 btn-secondary" id="show_mask_<?= $idname; ?>" data-toggle="hide" type="button"><span class="fa fa-eye-slash"></span></button>
	</div>
</div>
<script>
	$(function(){
		$('#show_mask_<?= $idname; ?>').on('click', function(){
			if($(this).data('toggle') == 'hide'){
				$(this).data('toggle', 'show');
				$(this).html('<span class="fa fa-eye"></span>');
				$('#<?= $idname; ?>').attr('type', 'text');
			} else {
				$(this).data('toggle', 'hide');
				$(this).html('<span class="fa fa-eye-slash"></span>');
				$('#<?= $idname; ?>').attr('type', 'password');
			}
		})
	})
</script>