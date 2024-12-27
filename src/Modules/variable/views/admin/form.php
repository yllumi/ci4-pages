<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
    </div>
</div>

<div class="card card-block">

    <?php if ($form_type == 'edit') : ?>
        Created at <?php echo time_ago($result['created_at']); ?>
    <?php endif; ?>

    <form id="post-form" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Variable Name</label>
            <input type="text" name="variable" value="<?php echo set_value('variable', $result['variable'] ?? ''); ?>" class="form-control" />
        </div>

        <div class="mb-3">
            <label class="form-label">Variable Value</label>
            <textarea name="value" class="form-control" rows="6"><?php echo set_value('value', $result['value'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Simpan</button>
    </form>
</div>