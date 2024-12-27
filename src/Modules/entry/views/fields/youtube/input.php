<input type="text" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" 
name="<?= $config['field'];?>" value="<?= $value ?? ''; ?>" 
placeholder="<?= $config['placeholder'] ?? '';?>" class="form-control" data-caption="<?= $config['label'];?>" />
