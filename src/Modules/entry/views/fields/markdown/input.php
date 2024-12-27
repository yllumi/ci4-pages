<textarea id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" class="form-control" name="<?= $config['field'];?>" data-caption="<?= $config['label'];?>"></textarea>
<script>
    var simplemde = new SimpleMDE({ 
    	element: document.getElementById('<?= $config['field'];?>'),
    	spellChecker: false,
    	forceSync: true
    });
    simplemde.value(`<?= $value;?>`);
</script>