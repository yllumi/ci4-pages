<?php if ($value ?? '') : ?>
    <input type="text" data-toggle="datepicker" id="<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>" value="<?= date('d-m-Y', strtotime($value)); ?>" class="form-control" data-caption="<?= $config['label']; ?>" autocomplete="off" />
<?php else : ?>
    <input type="text" data-toggle="datepicker" id="<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>" value="" class="form-control" data-caption="<?= $config['label']; ?>" autocomplete="off" />
<?php endif; ?>

<input type="hidden" id="real_<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>" name="<?= $config['field']; ?>" value="<?= $value; ?>">
<script>
    $(function() {
        $('#<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>').on('change', function() {
            let mydate = moment($(this).val(), "DD-MM-YYYY").format("YYYY-MM-DD");
            console.log(mydate);
            $('#real_<?= str_replace(['[', ']'], ['__', ''], $config['field']); ?>').val(mydate);
        })
    })
</script>