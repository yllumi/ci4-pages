<style>
  th {
    color: #666 !important;
    font-weight: 600;
    font-size: 14px;
    padding: 10px 10px !important;
  }

  th.sorted {
    color: #fe974b !important;
    font-weight: 700;
  }

  .no-border-top td {
    border-top: 0;
    padding-top: 0;
  }

  .select2-container .select2-selection--single {
    height: 31px !important;
  }
</style>

<div class="card rounded shadow">
  <div class="card-body">

    <form>
      <section class="mb-2">
        <div class="mb-4">
          <?php if (isPermitted($entryConf['override_permission']['insert'] ?? 'insert', $entry) && !($entryConf['disable_add'] ?? false)) : ?>
            <a href="<?= site_url($add_url) . '?' . $_SERVER['QUERY_STRING']; ?>" class="btn btn-success rounded shadow-sm">
              <span class="fa fa-plus"></span>
              <span class="d-none d-sm-inline">Tambah <?= $entryConf['name']; ?></span>
            </a>
          <?php endif; ?>

          <?php if (isPermitted($entryConf['override_permission']['export_csv'] ?? 'export_csv', $entry) && ($entryConf['export_csv'] ?? false)) : ?>
            <a data-pjax=false href="<?= site_url('admin/entry/' . $entry . '/export_csv' . '?' . $_SERVER['QUERY_STRING']); ?>" class="btn btn-outline-secondary rounded" target="_blank">
              <span class="fa fa-download"></span>
              <span class="d-none d-sm-inline">Export CSV</span>
            </a>
          <?php endif; ?>

          <?php if (isset($action_buttons['top'])) : ?>
            <?php foreach ($action_buttons['top'] as $key => $button) : ?>
              <?php if (!isset($button['menu_permission']) || isPermitted($button['menu_permission'], $entry)) : ?>
                <?php $topUrl = site_url("admin/entry/$entry/action/top/" . $key) . '?' . $_SERVER['QUERY_STRING'];
                if (($button['confirm'] ?? '') && is_array($button['confirm']))
                  $topUrl = site_url("admin/entry/$entry/confirm/top/" . $key) . '?' . $_SERVER['QUERY_STRING'];
                ?>
                <?php if ($button['modal'] ?? null) : ?>
                  <a href="#" data-url="<?= $button['modal'] . '?' . $_SERVER['QUERY_STRING']; ?>" data-caption="<?= $button['caption']; ?>" data-bs-toggle="modal" data-bs-target="#detailModal" class="btn btn-outline-secondary rounded <?= $key; ?>" title="<?= $button['caption']; ?>">
                  <?php else : ?>
                    <a href="<?= $topUrl; ?>" class="btn btn-outline-secondary rounded <?= $key; ?>" title="<?= $button['caption']; ?>">
                    <?php endif; ?>
                    <span class="fa fa-<?= $button['icon'] ?? 'heart'; ?>"></span>
                    <span class="d-none d-sm-inline"><?= $button['caption']; ?></span>
                    </a>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
        </div>

        <div class="row gx-3 align-items-center">
          <?php if ($show_total == true) : ?>
            <div class="col-auto">
              <div class="mb-2 px-1 pe-3 border-end py-2"><strong>Total baris: <?= $total; ?></strong></div>
            </div>
          <?php endif; ?>

          <?php if ($sorting == true) : ?>
            <div class="col-auto d-flex align-items-center">
              <div class="mb-2 me-sm-2 text-nowrap"><label>Sort by: </label></div>
              <?php
              $sort_fields['created_at'] = 'Tanggal submit';
              if ($show_on_table ?? []) {
                foreach ($show_on_table as $sotfield) {
                  $sort_fields[$sotfield] = $fields[$sotfield]['label'];
                }
              }
              ?>
              <?= form_dropdown('sort', $sort_fields, $this->input->get('sort'), 'class="form-select mb-2 me-sm-2"'); ?>
              <?= form_dropdown('sortdir', ['desc' => 'desc', 'asc' => 'asc'], $this->input->get('sortdir'), 'class="form-select mb-2 me-sm-2"'); ?>
            </div>
          <?php endif; ?>

          <?php if ($perpaging == true) : ?>
            <div class="col-auto d-flex align-items-center">
              <div class="mb-2 me-sm-2"><label>Perpage: </label></div>
              <?php $perpage_fields = array_combine([10, 20, 30, 40, 50, 80, 100], [10, 20, 30, 40, 50, 80, 100]); ?>
              <?= form_dropdown('perpage', $perpage_fields, ($this->input->get('perpage') ?? $entryConf['row_per_page'] ?? 10), 'class="form-control mb-2 me-sm-2"'); ?>
            </div>

            <div class="col-auto btn-group mb-2">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="<?= site_url($index_url); ?><?= isset($entryConf['parent_module_filter_field']) ? '?filter[' . $entryConf['parent_module_filter_field'] . ']=' . ($_GET['filter'][$entryConf['parent_module_filter_field']] ?? '') : ''; ?>" class="btn btn-secondary">Reset</a>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <div class="table-responsive">
        <table class="table table-hover <?= $fullwidth_table === false ? 'table-responsive' : ''; ?> <?= $small_table === true ? 'table-sm' : ''; ?>">
          <thead>
            <tr style="background-color: #eee;">
              <?php if ($show_numbering) : ?>
                <th width="60px">No.</th>
              <?php endif; ?>

              <?php if ($show_on_table ?? []) : ?>
                <?php foreach ($show_on_table as $tableField) : ?>
                  <th class="<?= $this->input->get('sort') == $tableField ? 'sorted' : ''; ?>">
                    <?php if (($sortdir = $this->input->get('sortdir')) && $this->input->get('sort') == $tableField) : ?>
                      <span class="fa fa-caret-<?= $sortdir == 'desc' ? 'down' : 'up'; ?> text-white"></span>
                    <?php endif; ?>
                    <?= $fields[$tableField]['label']; ?>
                  </th>
                <?php endforeach; ?>
              <?php endif; ?>

              <?php if ($show_timestamps) : ?>
                <?php foreach ($show_timestamps as $timestamp) : ?>
                  <th class="<?= ($this->input->get('sort') ?? 'created_at') == $timestamp ? 'sorted' : ''; ?>">
                    <label>
                      <?php if (($sortdir = $this->input->get('sortdir') ?? 'desc') && ($this->input->get('sort') ?? 'created_at') == $timestamp) : ?>
                        <span class="fa fa-caret-<?= $sortdir == 'desc' ? 'down' : 'up'; ?> text-white"></span>
                      <?php endif; ?>
                      <span class="text-nowrap"><?= $timestamp; ?></span>
                    </label>
                  </th>
                <?php endforeach; ?>
              <?php endif; ?>

              <th></th>
            </tr>
          </thead>
          <tbody>

            <?php if ($filtering == true) : ?>
              <tr>
                <?php if ($show_numbering) : ?>
                  <td></td>
                <?php endif; ?>

                <?php if ($show_on_table ?? []) : ?>
                  <?php foreach ($show_on_table as $tableField) : ?>
                    <td><?= form_filter($fields[$tableField]); ?></td>
                  <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($show_timestamps) : ?>
                  <?php foreach ($show_timestamps as $timestamp) : ?>
                    <td></td>
                  <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($entryConf['parent_module_filter_field'])) : ?>
                  <input type="hidden" name="filter[<?= $entryConf['parent_module_filter_field']; ?>]" value="<?= $_GET['filter'][$entryConf['parent_module_filter_field']] ?? ''; ?>">
                <?php endif; ?>

                <td class="text-end">
                  <div class="btn-group">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span></button>
                    <a href="<?= site_url($index_url); ?>
                    <?= isset($entryConf['parent_module_filter_field']) ? '?filter[' . $entryConf['parent_module_filter_field'] . ']=' . ($_GET['filter'][$entryConf['parent_module_filter_field']] ?? '') : ''; ?>" class="btn btn-secondary">
                      <span class="fa fa-refresh"></span>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endif; ?>

            <?php if (empty($results)) : ?>

              <tr>
                <td colspan="5">Belum ada data.</td>
              </tr>

            <?php else : ?>
              <?php
              $page = $_GET['page'] ?? 1;
              $perpage = $_GET['perpage'] ?? 10;
              $i = (($page - 1) * $perpage) + 1;
              foreach ($results as $result) : ?>
                <tr>
                  <?php if ($show_numbering) : ?>
                    <td><?= $i; ?></td>
                  <?php endif; ?>


                  <?php if ($show_on_table ?? []) : ?>
                    <?php foreach ($show_on_table as $tableField) : ?>
                      <td>
                        <?= generate_output($fields[$tableField], $result); ?>
                      </td>
                    <?php endforeach; ?>
                  <?php endif; ?>

                  <?php if ($show_timestamps) : ?>
                    <?php foreach ($show_timestamps as $timestamp) : ?>
                      <td title="<?php echo PHP81_BC\strftime("%A, %d %B", strtotime($result[$timestamp])), ci()->config->item('locale'); ?>">
                        <?php echo PHP81_BC\strftime("%d-%m-%Y, %H:%I", strtotime($result[$timestamp]), ci()->config->item('locale')); ?>
                      </td>
                    <?php endforeach; ?>
                  <?php endif; ?>

                  <td class="text-end">
                    <?php if (isset($action_buttons['row'])) : ?>
                      <div class="btn-group mb-1">
                        <?php foreach ($action_buttons['row'] as $key => $button) : ?>
                          <?php if (!isset($button['menu_permission']) || isPermitted($button['menu_permission'], $entry, isset($button['whitelist']) ? [$result[$button['whitelist']]] : [])) : ?>

                            <?php if (!isset($button['condition']) || compare_with_symbol($result[$button['condition'][0]], $button['condition'][1], $button['condition'][2] ?? '==')) : ?>

                              <?php if (($button['confirm'] ?? '') && is_array($button['confirm'])) : ?>
                                <a <?= $button['target'] ?? '' ? 'data-pjax=false target="'.$button['target'].'"' : 'target="_self"'; ?> href="<?php echo site_url("admin/entry/$entry/confirm/row/$key/" . $result['id']); ?>" class="btn btn-sm btn-secondary text-info <?= $key; ?>" title="<?= $button['caption']; ?>">
                                  <span class="fa fa-<?= $button['icon'] ?? 'heart'; ?>"></span> <?= $button['caption']; ?>
                                </a>

                              <?php elseif ($button['confirm'] ?? '') : ?>
                                <a <?= $button['target'] ?? '' ? 'data-pjax=false target="'.$button['target'].'"' : 'target="_self"'; ?> href="<?php echo site_url("admin/entry/$entry/action/row/$key/" . $result['id']); ?>" class="btn btn-sm btn-secondary text-info <?= $key; ?>" title="<?= $button['caption']; ?>" onclick="<?= is_string($button['confirm']) ? "return confirm('" . $button['confirm'] . "');" : "return confirm('Jalankan aksi ini?');" ?>">
                                  <span class="fa fa-<?= $button['icon'] ?? 'heart'; ?>"></span> <?= $button['caption']; ?>
                                </a>

                              <?php else : ?>
                                <a <?= $button['target'] ?? '' ? 'data-pjax=false target="'.$button['target'].'"' : 'target="_self"'; ?> href="<?php echo site_url("admin/entry/$entry/action/row/$key/" . $result['id']); ?>" class="btn btn-sm btn-secondary text-info <?= $key; ?>" title="<?= $button['caption']; ?>">
                                  <span class="fa fa-<?= $button['icon'] ?? 'heart'; ?>"></span> <?= $button['caption']; ?>
                                </a>
                              <?php endif; ?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>

                    <div class="btn-group mb-1">
                      <?php if ($entryConf['show_detail'] ?? true) : ?>
                        <button type="button" class="btn btn-sm btn-secondary text-secondary <?= ($entryConf['disable_detail'] ?? false) ? 'disabled' : ''; ?>" data-url="<?= 'admin/entry/' . $entry . '/detail/' . $result['id']; ?>" data-bs-caption="Detail data" data-bs-toggle="modal" data-bs-target="#detailModal" title="Detail"><span class="fa fa-search"></span> Detail</button>
                      <?php endif; ?>

                      <?php if (isPermitted($entryConf['override_permission']['update'] ?? 'update', $entry) && !($entryConf['hide_edit'] ?? false)) : ?>
                        <a class="btn btn-sm btn-secondary text-success <?= ($entryConf['disable_edit'] ?? false) ? 'disabled' : ''; ?>" href="<?= site_url($edit_url . '/' . $result['id']) . '?' . $_SERVER['QUERY_STRING']; ?>" title="Edit"><span class="fa fa-pencil"></span> Edit</a>
                      <?php endif; ?>

                      <?php if (isPermitted($entryConf['override_permission']['delete'] ?? 'delete', $entry)) : ?>
                        <a class="btn btn-sm btn-secondary text-danger <?= ($entryConf['disable_delete'] ?? false) ? 'disabled' : ''; ?>" onclick="return confirm('are you sure?')" href="<?= site_url($delete_url . '/' . $result['id']) . '?' . $_SERVER['QUERY_STRING']; ?>" title="<?= ($entryConf['disable_delete'] ?? false) ? 'Delete button is disabled' : 'Delete'; ?>"><span class="fa fa-remove"></span> Hapus</a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php $i++;
              endforeach; ?>

            <?php endif; ?>

          </tbody>
        </table>
      </div>

    </form>

    <?php if (isset($pagination)) : ?>
      <div class="pagination">
        <?php echo $pagination; ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-white text-dark">
        <h5 class="modal-title" id="detailModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="ratio ratio-16x9">
        <iframe id="detailIframe" src="#" frameborder="0"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
  $('#detailModal').on('show.bs.modal', function(event) {
    var modalButton = $(event.relatedTarget);
    var caption = $(event.relatedTarget).data('caption');
    var url = `<?= site_url(); ?>` + $(event.relatedTarget).data('url');
    $('#detailModalLabel').html(caption);
    $('#detailIframe').attr('src', url);
  })
  window.closeModal = function() {
    $('#detailModal').modal('hide');
    window.location.reload();
  };
</script>