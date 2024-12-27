<table class="table table-striped table-sm table-responsive">
	<thead>
		<tr>
			<th width="60px">id</th>
			<?php foreach ($fields as $field) : ?>
				<?php if ($field['datalist'] ?? '') : ?>
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

				<?php foreach ($fields as $field) : ?>
					<?php if ($field['datalist'] ?? '') : ?>
						<td><input type="text" class="form-control form-control-sm" name="filter[<?= $field['field']; ?>]" value="<?= $this->input->get("filter[{$field['field']}]", true); ?>" placeholder="filter by <?= $field['field']; ?>"></td>
					<?php endif; ?>
				<?php endforeach; ?>

				<td></td>
				<td class="text-end">
					<div class="btn-group">
						<button type="submit" class="btn btn-primary">Filter</button>
						<a href="<?= site_url('admin/variable'); ?>" class="btn btn-secondary">Reset</a>
					</div>
				</td>
			</tr>
		</form>
		<!-- End form filter -->

		<?php if (empty($results)) : ?>

			<tr>
				<td colspan="5">No record found ..</td>
			</tr>

		<?php else : ?>

			<?php foreach ($results as $result) : ?>
				<tr>
					<td><?= $result['id']; ?></td>

					<?php foreach ($fields as $field) : ?>
						<?php if ($field['datalist'] ?? '') : ?>
							<td><?= nl2br($result[$field['field']]); ?></td>
						<?php endif; ?>
					<?php endforeach; ?>

					<td title="<?php echo PHP81_BC\strftime("%A, %d %B", strtotime($result['created_at']), ci()->config->item('locale')); ?>">
						<?php echo PHP81_BC\strftime("%d-%m-%Y, %H:%I", strtotime($result['created_at']), ci()->config->item('locale')); ?>
					</td>
					<td class="text-end">
						<div class="btn-group">
							<a class="btn btn-sm btn-outline-success" href="<?php echo site_url('admin/variable/edit/' . $result['id']); ?>" title="Edit"><span class="fa fa-pencil"></span> Edit</a>
							<a class="btn btn-sm btn-outline-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/variable/delete/' . $result['id']); ?>" title="Delete"><span class="fa fa-remove"></span> Delete</a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>

		<?php endif ?>

	</tbody>
</table>

<?php if (isset($pagination)) : ?>
	<div class="pagination">
		<?php echo $pagination; ?>
	</div>
<?php endif; ?>