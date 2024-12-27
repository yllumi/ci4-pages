<input type="text" 
	   id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" 
	   class="form-control <?= $value ? '' : 'slugify'; ?>" 
	   name="<?php echo $config['field'];?>" 
	   value="<?php echo $value;?>" 
	   data-referer="<?php echo $config['referer'];?>"
	   data-caption="<?= $config['label'];?>" >