<input 
    type="text" 
    id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" 
    name="<?= $config['field'];?>" 
    value="<?= $value ?? ''; ?>"
    class="color form-control col-md-2 col-6" />
