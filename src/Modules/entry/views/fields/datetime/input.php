<?php $fieldname = str_replace(['[', ']'], ['__', ''], $config['field']); ?>
<?php if ($value ?? '') : ?>
    <div class="d-flex">
        <input type="text" id="<?= $fieldname; ?>_date" value="<?= date('d-m-Y', strtotime($value)); ?>" class="form-control me-1" style="max-width:150px" data-caption="<?= $config['label']; ?>" autocomplete="off" data-toggle="datepicker" />
        <input type="time" id="<?= $fieldname; ?>_time" value="<?= date('H:i', strtotime($value)); ?>" class="form-control" style="max-width:150px" data-caption="<?= $config['label']; ?>" />
    </div>
<?php else : ?>
    <div class="d-flex">
        <input type="text" id="<?= $fieldname; ?>_date" value="" class="form-control me-1" style="max-width:150px" data-caption="<?= $config['label']; ?>" autocomplete="off" data-toggle="datepicker" />
        <input type="time" id="<?= $fieldname; ?>_time" value="" class="form-control" style="max-width:150px" data-caption="<?= $config['label']; ?>" />
    </div>
<?php endif; ?>

<input type="hidden" id="real_<?= $fieldname; ?>" name="<?= $config['field']; ?>" value="<?= $value; ?>">
<script>
    $(function() {
        $('#<?= $fieldname; ?>_date, #<?= $fieldname; ?>_time').on('change', function() {
            let mydate = moment($('#<?= $fieldname; ?>_date').val(), "DD-MM-YYYY").format("YYYY-MM-DD");
            let mytime = $('#<?= $fieldname; ?>_time').val();
            // console.log(mydate + mytime);
            $('#real_<?= $fieldname; ?>').val(mydate + ' ' + mytime + ':00');
        })
    })
</script>