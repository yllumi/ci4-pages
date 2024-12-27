<?php

$idname = str_replace(['[',']'], ['__',''], $config['field']);

// Relation model
$itemModel = setup_entry_model($config['relation']['entry']);
$itemData = $itemModel->getAll();

// Saveto model
$savetoModel = setup_entry_model($config['relation']['saveto']['entry']);
$data = $savetoModel->with_stock()->where($config['relation']['saveto']['foreign_key'], $id ?? 0)->getAll();
?>

<div>
  <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addMultipleItemModal-<?= $config['field']; ?>">+ add item</button>
  <input type="hidden" name="<?= $config['field']; ?>[mode]" value="multiple_advanced">
  <input type="hidden" name="<?= $config['field']; ?>[table]" value="<?= $config['relation']['saveto']['table']; ?>">
  <input type="hidden" name="<?= $config['field']; ?>[foreign_key]" value="<?= $config['relation']['saveto']['foreign_key']; ?>">

  <table class="table table-striped table-sm table-responsive" id="table-<?= $idname; ?>">
    <tr>
      <?php foreach ($savetoModel->show_on_table as $tableField): ?>
        <th><?= $savetoModel->fields[$tableField]['label']; ?></th>
      <?php endforeach; ?>
      <th></th>
    </tr>

    <?php if($data): ?>
      <?php foreach ($data as $row):?>
        <tr>
          <?php foreach ($savetoModel->show_on_table as $tableField): ?>
            <td class="cell <?= $tableField; ?>" data-value="<?= $row[$tableField]; ?>">
              <?= generate_output($savetoModel->fields[$tableField], $row); ?>
            </td>
          <?php endforeach; ?>
          <td>
            <a href="#" class="remove-row"><span class="fa fa-remove"></span></a>
            
            <?php foreach ($savetoModel->fields as $saveField => $saveValue): ?>
              <?php $rowJson[$saveField] = $row[$saveField]; ?>
            <?php endforeach; ?>
            <input type='hidden' name='<?= $config['field']; ?>[data][]' value='<?= json_encode($rowJson); ?>'>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="addMultipleItemModal-<?= $idname; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah <?= $config['label']; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalForm-<?= $idname; ?>">
          <?php foreach($savetoModel->fields as $itemField): ?>
            <?php if($itemField['field'] != $config['relation']['saveto']['foreign_key']): ?>
              <div class="form-group">
                <label><?= $itemField['label']; ?></label>
                <?= ($itemField['description'] ?? '') ? '<small> &bull; '.$itemField['description'].'</small>' : ''; ?>
                <?= generate_input($itemField, null); ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="btnSave-<?= $idname; ?>" type="button" class="btn btn-primary">Save changes</button>
        </div>
    </div>
  </div>
</div>
<script>
  $(function(){
    $('#btnSave-<?= $idname; ?>').on('click', function(){
      let data = [];
      $('#modalForm-<?= $idname; ?>').find('input,select,radio,checkbox').each(function(i){
        data[$(this).attr('id')] = $(this).val();
      })
      let output = Object.assign({}, data);
      let outputJSON = JSON.stringify(output);

      let table = $('#table-<?= $idname; ?>');
      let rowTable = '<tr>';
      for(let prop in output){
        rowTable += `<td class="cell ${prop}" data-value="${output[prop]}">${output[prop]}</td>`;
      }
      rowTable += `<td>
            <a href="#" class="remove-row"><span class="fa fa-remove"></span></a>
            <input type='hidden' name='<?= $config['field']; ?>[data][]' value='${outputJSON}'>
          </td>`;
      rowTable += '<tr>';
      table.append(rowTable);
      $('#addMultipleItemModal-<?= $idname; ?>').modal('close');
    });

    $('.remove-row').on('click', function(){
      $(this).parents('tr').remove();
    })
  })
</script>