<div class="mb-3">
	<div class="row">
		<div class="col-lg-6">
			<h2><?=t($page_title)?></h2>
		</div>
		<div class="col-lg-6 text-end">
			<?php if(isPermitted('add_role', 'user')): ?>
			<a href="<?php echo site_url('admin/user/role/add'); ?>" class="btn btn-secondary shadow-sm">
				<span class="fa fa-plus"></span> <?=t('Add Role')?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php echo $this->session->flashdata('message');?>

<div class="card rounded-xl shadow">
    <div class="card-body table-responsive">

		<table class="table table-striped">
			<thead>
				<tr>
					<th width="60px">id</th>
					<?php foreach ($fields as $field): ?>
						<?php if($field['datalist'] ?? ''): ?>
							<th><?= $field['label']; ?></th>
						<?php endif; ?>
					<?php endforeach; ?>
					<th>Created at</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<!-- Form filter -->
				<form>
					<tr>
						<td>
							<input type="text" class="form-control form-control-sm" name="filter[id]" value="<?= $this->input->get("filter[id]", true); ?>">
						</td>

						<?php foreach ($fields as $field): ?>
							<?php if($field['datalist'] ?? ''): ?>
								<td><input type="text" class="form-control form-control-sm" name="filter[<?= $field['field']; ?>]" value="<?= $this->input->get("filter[{$field['field']}]", true); ?>" placeholder="filter by <?= $field['field']; ?>"></td>
							<?php endif; ?>
						<?php endforeach; ?>

						<td></td>
						<td class="text-end">
							<div class="btn-group">
								<button type="submit" class="btn btn-primary">Filter</button>
								<a href="<?= site_url('admin/user/role'); ?>" class="btn btn-secondary">Reset</a>
							</div>
						</td>
					</tr>
				</form>
				<!-- End form filter -->

				<?php if (empty($results)): ?>

					<tr><td colspan="5">No record found ..</td></tr>

				<?php else: ?>

					<?php foreach ($results as $result): ?>
						<tr>
							<td><?= $result['id']; ?></td>

							<?php foreach ($fields as $field): ?>
								<?php if($field['datalist'] ?? ''): ?>
									<td><?php echo $result[$field['field']];?></td>
								<?php endif; ?>
							<?php endforeach; ?>

							<td title="<?php echo PHP81_BC\strftime("%A, %d %B", strtotime($result['created_at'] ?? 0), ci()->config->item('locale'));?>">
								<?php echo PHP81_BC\strftime("%d-%m-%Y, %H:%I", strtotime($result['created_at'] ?? 0), ci()->config->item('locale'));?>
							</td>
							<td class="text-end">
								<?php if($result['role_name'] == 'Super'): ?>

								<em><?=t('Super admin has all permissions')?>.</em>

								<?php else: ?>

								<?php if(isPermitted('manage_privileges', 'user')): ?>
								<a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('admin/user/role/privileges/' . $result['id']); ?>" title="Set Privilege"><span class="fa fa-key"></span> <?=t('Set Privileges')?></a>
								<?php endif; ?>

								<div class="btn-group">
									<?php if(isPermitted('edit_role', 'user')): ?>
									<a class="btn btn-sm btn-outline-success" href="<?php echo site_url('admin/user/role/edit/'. $result['id']); ?>" title="Edit"><span class="fa fa-pencil"></span> Edit</a> 
									<?php endif; ?>

									<?php if(isPermitted('delete_role', 'user')): ?>
									<a class="btn btn-sm btn-outline-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/user/role/delete/' . $result['id']); ?>" title="<?=t('Delete')?>"><span class="fa fa-remove"></span> Delete</a>
									<?php endif; ?>
								</div>

								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>

				<?php endif ?>

			</tbody>
		</table>

		<?php if(isset($pagination)) : ?>
			<div class="pagination">
				<?php echo $pagination; ?>
			</div>
		<?php endif; ?>

	</div>
</div>