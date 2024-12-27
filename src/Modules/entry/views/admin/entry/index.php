<!-- Header Page -->
<div class="mb-3">
  <div class="row">
    <div class="col-6">
      <ol class="breadcrumb">
        <?php if (isset($parent_url)) : ?>
          <?php foreach ($parent_url as $parentCaption => $parentUrl) : ?>
            <?php if(is_null($parentUrl)): ?>
              <li class="breadcrumb-item active"><?= $parentCaption ?? config_item('entries')[$entryConf['parent_module']]['name']; ?></li>
            <?php else: ?>
              <li class="breadcrumb-item <?= is_null($parentUrl) ? 'active' : '';?>"><a href="<?= site_url($parentUrl); ?>"><?= $parentCaption ?? config_item('entries')[$entryConf['parent_module']]['name']; ?></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php elseif ($entryConf['parent_module'] ?? '') : ?>
          <li class="breadcrumb-item"><a href=" <?= site_url('admin/entry/' . $entryConf['parent_module']); ?>"><?= config_item('entries')[$entryConf['parent_module']]['name']; ?></a></li>
        <?php endif; ?>
      </ol>
      <h2>
        <?php echo $page_title; ?>
      </h2>
    </div>
  </div>
</div>
<!-- End Header Page -->

<!-- Parent Data -->
<?php if (isset($parent_data)) : ?>
  <div class="card card-sm rounded shadow mb-2">
    <div class="card-body px-3 py-2">
      <h4 class="pt-2 d-flex justify-content-between">
        <span style="line-height:36px;"><?= array_shift($parent_data); ?></span>
        <a class="btn btn-secondary text-dark" data-bs-toggle="collapse" role="button" href="#detailEntry" aria-expanded="false" aria-controls="detailEntry">
          Lihat detail <span class="fa fa-chevron-down"></span>
        </a>
      </h4>
      <div class="collapse" id="detailEntry">
        <div class="row" style="margin:0">
          <div class="col-12">
            <table class="table table-sm table-responsive table-hover my-2">
              <?php foreach ($parent_data as $parent_title => $parent_value) : ?>
                <tr>
                  <td class="text-secondary d-flex justify-content-between" style="min-width:200px">
                    <strong><?= $parent_title; ?></strong>
                    <span>:</span>
                  </td>
                  <td><?= $parent_value; ?></td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<!-- End Parent Data -->

<div id="beforeTable"><?= $beforeTable ?? ''; ?></div>

<!-- Entry Table -->
<?php echo $table; ?>

<div id="beforeTable"><?= $afterTable ?? ''; ?></div>