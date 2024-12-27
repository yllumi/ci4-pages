<?php
$elmId = str_replace(['[', ']'], ['__', ''], $config['field']);
if ($value)
    $filename = pathinfo($value)['filename'];
else
    $filename = $entry . '_' . $elmId . '_' . random_string();
?>
<div id="preview_<?= $elmId; ?>">
<?php if($value): ?>
<img src="<?= $value; ?>" alt="">
<?php endif; ?>
</div>
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal_<?= $elmId; ?>">Buat Tanda Tangan</button><br>
<input type="text" style="opacity:0;height:2px;" id="value_<?= $elmId; ?>" name="<?= $elmId; ?>" value="<?= $value ? $value : '';?>" autocomplete="off" <?= strpos($config['rules'] ?? '', 'required') !== false ? 'required' : ''; ?>>

<!-- Modal -->
<div class="modal fade" id="modal_<?= $elmId; ?>" tabindex="-1" aria-labelledby="modal_<?= $elmId; ?>Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Bubuhkan Tanda Tangan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-end">
                <div class="text-center">
                    <canvas class="border" id="<?= "sketchpad_" . $elmId; ?>"></canvas><br>
                    <input type="hidden" id="base64_<?= $elmId; ?>" value="">
                    <button type="button" class="btn btn-sm btn-secondary" id="clear_<?= $elmId; ?>"><span class="fa fa-arrows-rotate"></span> Ulangi</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-save_<?= $elmId; ?>" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const canvas = document.getElementById("sketchpad_<?= $elmId; ?>");
        const signaturePad = new SignaturePad(canvas);
        var dataURL = '';

        signaturePad.addEventListener("endStroke", () => {
            dataURL = signaturePad.toDataURL();
            $('#base64_<?= $elmId; ?>').val(dataURL);
            console.log("Signature updated");
        });

        $('#clear_<?= $elmId; ?>').on('click', function() {
            signaturePad.clear();
        })

        $('#btn-save_<?= $elmId; ?>').on('click', function() {
            $.post('<?= site_url('entry/uploadBase64Image'); ?>', {
                    base64: $('#base64_<?= $elmId; ?>').val(),
                    filename: '<?= $filename; ?>'
                },
                function(data) {
                    var output = JSON.parse(data);
                    $('#preview_<?= $elmId; ?>').empty();
                    $('#preview_<?= $elmId; ?>').html(`<img src="${output.path + '?' + (new Date().getTime())}" />`);
                    $('#value_<?= $elmId; ?>').val(output.path);
                    $('#modal_<?= $elmId; ?>').modal('hide');
                });
        })
    })
</script>