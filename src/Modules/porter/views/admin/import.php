<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
    </div>
</div>

<div class="card card-block">

    <form id="post-form" method="post" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Upload CSV File</label>
            <?php $uploadField = [
                'field' => 'csv_file',
                'label' => 'CSV File',
                'form'  => 'file' 
            ];?>
            <?= generate_input($uploadField); ?>
        </div>

        <button type="submit" class="btn btn-outline-success"><span class="fa fa-upload"></span> Import</button>
    </form>
</div>

<?php if($header ?? null): ?>
<div class="card card-block mb-5">
    <h4>Pratinjau</h4>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <tr class="table-info">
                <?php foreach($header as $field): ?>
                    <th><?= $field; ?></th>
                <?php endforeach;?>
            </tr>
            <?php foreach($data as $row): ?>
                <tr>
                    <?php foreach($row as $cell): ?>
                        <td><?= $cell; ?></td>
                    <?php endforeach;?>
                </tr>
            <?php endforeach;?>
        </table>
    </div>

    <div class="mt-3">
        <?= form_open('admin/porter/processImport/'.$slug); ?>
        <input type="hidden" name="file" value="<?= $file; ?>">
        <button type="submit" class="btn btn-outline-primary">Import Data</button>
        <?= form_close(); ?>
    </div>
</div>
<?php endif; ?>