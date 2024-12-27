<?php
use Symfony\Component\Yaml\Yaml;

if (($config['mode'] ?? null) == 'yaml' && is_array($value))
    $value = Yaml::dump($value, 4);
?>
<?php $fieldName = str_replace(['[', ']'], ['__', ''], $config['field']); ?>
<div id="<?= $fieldName; ?>_editor" class="code_editor mb-4" style="min-height:<?= $config['height'] ?? '400'; ?>px"><?= $value; ?></div>
<input type="hidden" name="<?php echo $config['field']; ?>" id="<?= $fieldName; ?>" value="<?= htmlentities($value); ?>">

<script>
    $(function() {
        var <?= $fieldName; ?>_editor = ace.edit("<?= $fieldName; ?>_editor");
        <?= $fieldName; ?>_editor.session.setMode("ace/mode/<?= $config['mode'] ?? 'html'; ?>");
        document.getElementById('<?= $fieldName; ?>_editor').style.fontSize = '16px';
        <?= $fieldName; ?>_editor.session.setUseWrapMode(true);
        <?= $fieldName; ?>_editor.session.setOption('tabSize', 2);

        <?= $fieldName; ?>_editor.session.on('change', function(delta) {
            $('#<?= $fieldName; ?>').val(<?= $fieldName; ?>_editor.getValue());
        });
    })
</script>