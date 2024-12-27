<style>
    .modules {
        column-count: 3;
        column-gap: 1em;
    }

    .module {
        display: inline-block;
        margin: 0 0 1em;
        width: 100%;
    }

    .form-check-input {
        vertical-align: text-bottom;
    }
</style>

<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
    </div>
</div>

<div class="card card-block">

    <form id="post-form" method="post" action="<?php echo site_url('admin/user/role/update_role_privileges'); ?>" enctype="multipart/form-data">

        <div class="pb-2 text-end">
            <button type="submit" class="btn btn-outline-success btn-lg"><span class="fa fa-save"></span> Update</button>
        </div>

        <input type="hidden" name="role_id" value="<?php echo $role_id; ?>" />

        <div class="form-group mb-4">
            <h4><?= t('Role'); ?></h4>
            <?php echo form_dropdown('role_id', $roles, $role_id, 'id="role_id" class="form-control"'); ?>
        </div>

        <div class="form-group">

            <h4 class="mb-4 bg-info text-white p-2"><?= t('Module Privileges'); ?></h4>
            <div class="modules">

                <?php foreach ($module_privileges as $module => $list) :
                    if (empty($list)) continue; ?>

                    <div class="mb-4 module">
                        <h4><?= t(ucwords(str_replace('_', ' ', $module))); ?></h4>

                        <?php foreach ($list as $privilege) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privileges[]" id="<?= $module . '-' . $privilege; ?>" value="<?= $module . '.' . $privilege; ?>" <?= isset($role_privileges[$module]) && in_array($privilege, $role_privileges[$module]) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="<?= $module . '-' . $privilege; ?>"><?php echo $privilege; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="form-group">

            <h4 class="mb-4 bg-info text-white p-2"><?= t("Entry Privileges"); ?></h4>
            <div class="modules">

                <?php foreach ($entry_privileges as $module => $list) :
                    if (empty($list)) continue; ?>

                    <div class="mb-4 module">
                        <h4><?= t(ucwords(str_replace('_', ' ', $module))); ?></h4>

                        <?php foreach ($list as $privilege) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privileges[]" id="<?= $module . '-' . $privilege; ?>" value="<?= $module . '.' . $privilege; ?>" <?= isset($role_privileges[$module]) && in_array($privilege, $role_privileges[$module]) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="<?= $module . '-' . $privilege; ?>"><?php echo $privilege; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="border-top pt-3 text-end">
            <button type="submit" class="btn btn-outline-success btn-lg"><span class="fa fa-save"></span> Update</button>
        </div>
    </form>
</div>

<script>
    $('#role_id').on('change', function() {
        window.location.href = '<?= site_url('admin/user/role/privileges'); ?>/' + $(this).val();
    })
</script>