<?php $id = str_replace(['[', ']'], ['__', ''], $config['field']); ?>
<input id="<?= $id; ?>" type="hidden" name="<?php echo $config['field']; ?>" value="<?php echo $value; ?>">
<?php if ($value) {
    list($h, $m, $s) = explode(':', $value);
} ?>
<div class="d-flex justify-content-start">
    <div class="input-group mb-2 me-2" style="max-width:150px">
        <input id="<?= $id; ?>_h" type="number" class="form-control <?= $id; ?>_duration" value="<?= $h ?? '00'; ?>" min="0" max="24">
        <div class="input-group-text px-1 px-md-2"><?= t('hour'); ?></div>
    </div>
    <div class="input-group mb-2 me-2" style="max-width:150px">
        <input id="<?= $id; ?>_m" type="number" class="form-control <?= $id; ?>_duration" value="<?= $m ?? '00'; ?>" min="0" max="59">
        <div class="input-group-text px-1 px-md-2"><?= t('minute'); ?></div>
    </div>
    <div class="input-group mb-2 me-2" style="max-width:150px">
        <input id="<?= $id; ?>_s" type="number" class="form-control <?= $id; ?>_duration" value="<?= $s ?? '00'; ?>" min="0" max="59">
        <div class="input-group-text px-1 px-md-2"><?= t('second'); ?></div>
    </div>
</div>


<script>
    $(function() {
        $('.<?= $id; ?>_duration').on('change', function() {
            let h = $('#<?= $id; ?>_h').val() ?? '0';
            let m = $('#<?= $id; ?>_m').val() ?? '0';
            let s = $('#<?= $id; ?>_s').val() ?? '0';
            h = (new Intl.NumberFormat('en-US', {
                minimumIntegerDigits: 2
            })).format(h);
            m = (new Intl.NumberFormat('en-US', {
                minimumIntegerDigits: 2
            })).format(m);
            s = (new Intl.NumberFormat('en-US', {
                minimumIntegerDigits: 2
            })).format(s);
            $('#<?= $id; ?>').val(`${h}:${m}:${s}`);
        })
    })
</script>