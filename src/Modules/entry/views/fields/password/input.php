<div class="input-group">
	<input type="password" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" 
	name="<?= $config['field'];?>"
	placeholder="<?= $config['placeholder'] ?? '';?>" 
	class="form-control" 
	data-caption="<?= $config['label'];?>" />
	
	<div class="input-group-append">
	   <button class="btn mb-0 btn-secondary" id="show_password_<?= str_replace(['[',']'], ['__',''], $config['field']);?>" data-toggle="hide" type="button"><span class="fa fa-eye-slash"></span></button>
	</div>
</div>
<script>
	$(function(){
		$('#show_password_<?= str_replace(['[',']'], ['__',''], $config['field']);?>').on('click', function(){
			if($(this).data('toggle') == 'hide'){
				$(this).data('toggle', 'show');
				$(this).html('<span class="fa fa-eye"></span>');
				$('#<?= str_replace(['[',']'], ['__',''], $config['field']);?>').attr('type', 'text');
			} else {
				$(this).data('toggle', 'hide');
				$(this).html('<span class="fa fa-eye-slash"></span>');
				$('#<?= str_replace(['[',']'], ['__',''], $config['field']);?>').attr('type', 'password');
			}
		})
	})
</script>