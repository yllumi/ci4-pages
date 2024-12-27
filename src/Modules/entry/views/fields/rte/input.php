<?php
$akey = md5($_ENV['SITENAME'] . $_ENV['ENC_KEY']);
$fieldName = str_replace(['[', ']'], ['__', ''], $config['field']);
$fieldID = $config['id'] ?? $fieldName;
?>

<div class="editor-container" id="<?= $fieldID; ?>_container" data-id="<?= $fieldID; ?>">
    <div id="<?= $fieldID; ?>_editor"><?= $value; ?></div>
    <input type="hidden" class="<?= $fieldID; ?>_input_text" name="<?php echo $config['field']; ?>" id="<?= $fieldID; ?>_hidden" value="<?= htmlentities($value); ?>" <?= $config['minlength'] ?? null ? 'minlength="'. $config['minlength'].'"': ''; ?>>
    <input type="text" style="display:none" id="<?= $fieldID; ?>__entry__rfm_image_input" value="">
</div>

<?php if (isset($config['minlength'])) :?>
    <div><small><?= $config['label'];?> wajib diisi minimal <?php echo $config['minlength'];?> karakter.</small></div>
<?php endif;?>

<script>
    $(function() {
        ClassicEditor.create(document.querySelector('#<?= $fieldID; ?>_editor'), {
                placeholder: 'Tulis konten disini ..'
            })
            .then(editor => {

                myckeditor['<?= $fieldID; ?>'] = editor;
                myckeditor['<?= $fieldID; ?>'].model.document.on('change:data', () => {
                    $('#<?= $fieldID; ?>_hidden').val(myckeditor['<?= $fieldID; ?>'].getData());
                });
                
            })
            .catch(error => {
                console.error('There was a problem initializing the editor.', error);
            });


        $('.editor-container#<?= $fieldID; ?>_container').on('click', '.rfm_image', function() {
            $.fancybox.open({
                src: `<?= base_url(); ?>filemanager/dialog.php?type=1&field_id=<?= $fieldID; ?>__entry__rfm_image_input&akey=<?= $akey; ?>`,
                type: 'iframe'
            });
        })

    }) 
</script>

<?php if (isset($config['minlength'])) :?>
    <script>
    /**
     * Handle form count
     * 
     * Karena CKE word cound dan limiter plugin berbayar, kepaksa bikin sendiri.
     */
    $("form").submit(function(e){
        
        let id = "<?= $fieldID; ?>";

        let count = $('.' + id + '_input_text').val().length;
        let minlength = "<?php echo $config['minlength'];?>";

        if (count <= minlength) {
            alert('Minimal pengisian catatan adalah '+ minlength +' karakter, harap isi catatan selengkap-lengkapnya, sekarang baru terisi '+ count +' karakter.');
            
            return false;
        } else {
            // Boleh lanjut.
            $("form").submit();
        }
    });
    </script>
<?php endif;?>