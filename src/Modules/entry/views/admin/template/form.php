<div class="card card-block">
  <form method="post" enctype="multipart/form-data">

    <?php if (count($fields) > 5) : ?>
      <div class="mb-5 pb-3 border-bottom">
        <button type="submit" name="submitBtn" value="save" class="btn btn-success me-1"><span class="fa fa-save"></span> Save</button>
        <button type="submit" name="submitBtn" value="save_and_exit" class="btn btn-outline-success me-1"><span class="fa fa-save"></span> Save &amp; Exit</button>
        <a href="<?php echo site_url($index_url) . '?' . $_SERVER['QUERY_STRING']; ?>" class="btn btn-secondary"><span class="glyphicon glyphicon-menu-left"></span> Back to Entry</a>
      </div>
    <?php endif; ?>

    <div class="row">

      <?php foreach ($fields as $field => $fieldConf) : ?>

        <div class="<?= ($fieldConf['fullwidth'] ?? false) ? 'col-12' : 'col-xl-8 col-lg-9 col-md-8'; ?> <?= $fieldConf['hide_input'] ?? 0 ? 'sr-only' : ''; ?>">
          <div class="mb-3">
            <?php if (!($fieldConf['hide_label'] ?? false)) : ?>
              <label class="form-label"><?php echo $fieldConf['label']; ?></label>
              <?php if (strpos($fieldConf['rules'] ?? '', 'required') !== false) : ?>
                <span class="text-danger">*</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($fieldConf['description'] ?? '') : ?>
              <small> &bull; <?php echo $fieldConf['description']; ?></small>
            <?php endif; ?>

            <div class="d-block">
              <?php if (($fieldConf['skip_input'] ?? '') == true) : ?>
                <div class="border p-2" style="background-color:#eee;">
                  <?php echo generate_output($fieldConf, $result[$field] ?? null, $options ?? ''); ?>
                </div>
              <?php else : ?>
                <?php echo generate_input($fieldConf, $result[$field] ?? null, $options ?? ''); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-sm-4">
          <small class="form-text"><?php echo $this->session->flashdata('form_error_' . $field); ?></small>
        </div>

      <?php endforeach; ?>

    </div>

    <hr class="my-4">

    <?php if ($entryConf['edit_after_insert'] ?? true) : ?>
      <input type="hidden" name="edit_after_insert" value="1">
    <?php endif; ?>

    <div class="">
      <button type="submit" name="submitBtn" value="save" class="btn btn-success me-1"><span class="fa fa-save"></span> Save</button>
      <button type="submit" name="submitBtn" value="save_and_exit" class="btn btn-outline-success me-1"><span class="fa fa-save"></span> Save &amp; Exit</button>
      <a href="<?php echo site_url($index_url) . '?' . $_SERVER['QUERY_STRING']; ?>" class="btn btn-secondary"><span class="glyphicon glyphicon-menu-left"></span> Back to Entry</a>
    </div>

  </form>
</div>