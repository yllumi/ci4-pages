<style>
  th {color: #666 !important; font-weight: 600; font-size: 14px; padding: 10px 10px !important;}
  th.sorted {color: #ffbf4a !important;font-weight: 700;}
  .resetcache { position: absolute;top: 20px;left: 30px; }
</style>

<div class="mb-3">
	<div class="row">
		<div class="col-lg-6">
			<h2><?=t($page_title); ?></h2>
		</div>
        <div class="col-lg-6 text-end">
            <?php if(isPermitted('export', 'user')): ?>
            <a href="<?php echo site_url('admin/user/export?'.$_SERVER['QUERY_STRING'])?>" class="btn btn-secondary shadow-sm"><span class="fa fa-download"></span> <?=t('Export');?></a>
            <?php endif; ?>

            <?php if(isPermitted('add', 'user')): ?>
            <a href="<?php echo site_url('admin/user/add')?>" class="btn btn-secondary shadow-sm"><span class="fa fa-user-plus"></span> <?=t('Add User');?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $this->session->flashdata('message');?>

<div class="card rounded-xl shadow">
    <div class="card-body">

    <div class="mb-4">
        <div class="row mx-1">
            <div class="col-12 col-sm-4 text-center border p-2"><strong>Total Users</strong>
                <br><?php echo $stat['all'];?> orang</div>
            <div class="col-12 col-sm-4 text-center border p-2"><strong>Active Users</strong>
                <br><?php echo $stat['active'];?> orang</div>
            <div class="col-12 col-sm-4 text-center border p-2"><strong>Pending/Blocked</strong>
                <br><?php echo $stat['inactive'];?> orang</div>
        </div>
        <a class="resetcache" href="<?= site_url('admin/user/reset_cache');?>"><span class="fa fa-refresh"></span></a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

                <form>
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" name="filter_id" value="<?= $this->input->get('id', true); ?>" placeholder="id"></td>
                        <td><input type="text" class="form-control form-control-sm" name="filter_name" value="<?= $this->input->get('filter_name', true); ?>" placeholder="Name"></td>
                        <td><input type="text" class="form-control form-control-sm" name="filter_username" value="<?= $this->input->get('filter_username', true); ?>" placeholder="Username"></td>
                        <td>
                            <?= form_dropdown('filter_role_id', $roles, $this->input->get('filter_role_id', true), 'class="form-control form-control-sm"'); ?>
                        </td>
                        <td><input type="text" class="form-control form-control-sm" name="filter_email" value="<?= $this->input->get('filter_email', true); ?>" placeholder="Email"></td>
                        <td></td>
                        <td>
                            <?= form_dropdown('filter_status', $status, $this->input->get('filter_status', true), 'class="form-control form-control-sm"'); ?>
                        </td>
                        <td></td>
                        <td>
                            <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="<?= site_url('admin/user'); ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </td>
                    </tr>
                </form>

                <?php if (empty($results)): ?>
                    <tr><td colspan="5">No record found ..</td></tr>
                <?php else: ?>

                    <?php $i = 1; foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['role']['role_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo ($row['source_id'] != null) ? 'Codepolitan' : '-'; ?></td>
                            <td>
                                <?php if ($row['status'] == 'active'):?>
                                    <span class="badge badge-success"><?php echo $row['status'];?></span>
                                <?php else:?>
                                    <span class="badge badge-danger"><?php echo $row['status'];?></span>
                                <?php endif;?>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <?php if($row['status'] == 'inactive'): ?>
                                        <?php if(isPermitted('activate', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-success" href="<?php echo site_url('admin/user/activate/' . $row['id']); ?>" onclick="return confirm('Email belum tervalidasi. Lanjutkan aktivasi?')">Activate</a> 
                                        <?php endif; ?>
                                        
                                        <?php if(isPermitted('edit', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('admin/user/edit/' . $row['id']); ?>">Edit</a>
                                        <?php endif; ?>

                                        <?php if(isPermitted('delete', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/user/delete/' . $row['id']); ?>">Delete</a>
                                        <?php endif; ?>

                                    <?php elseif($row['status'] == 'deleted'): ?>
                                        
                                        <?php if(isPermitted('edit', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('admin/user/edit/' . $row['id']); ?>">Edit</a>
                                        <?php endif; ?>
                                        
                                        <?php if(isPermitted('delete', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-secondary" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/user/block/' . $row['id']); ?>">Undelete</a>
                                        <?php endif; ?>
                                        
                                        <?php if(isPermitted('purge', 'user')): ?>
                                        <a class="btn btn-sm btn-danger" onclick="return confirm('Purge data will hard delete and cannot be restored. Continue?')" href="<?php echo site_url('admin/user/purge/' . $row['id']); ?>">Purge</a>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <?php if(isPermitted('edit', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('admin/user/edit/' . $row['id']); ?>">Edit</a>
                                        <?php endif; ?>
                                        
                                        <?php if(isPermitted('block', 'user')): ?>
                                        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/user/block/' . $row['id']); ?>">Block</a>
                                        <?php endif; ?>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>

                    <?php $i++; endforeach; ?>

                <?php endif; ?>

            </tbody>
        </table>

        <?php if(isset($pagination)) : ?>
            <div class="pagination">
                <?php echo $pagination; ?>
            </div>
        <?php endif; ?>

        </div>
    </div>
</div>