<div class="row heading">
	<div class="col-md-6">
		<h2><?= $page_title; ?></h2>
	</div>
</div>
<br>


<div class="card">
	<?php if ($form_type == 'edit') : ?>
		Created at <?php echo time_ago($result['created_at']); ?>
	<?php endif; ?>

	<form id="post-form" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data">

		<div class="card-body <?= $form_type == 'new' ? 'slugify' : ''; ?>">

			<div class="mb-3">
				<label class="form-label">Area Name</label>
				<?php echo form_input('area_name', set_value('area_name', $result['area_name'] ?? ''), 'class="title form-control"'); ?>
			</div>
			<div class="mb-3">
				<label class="form-label">Area slug</label>
				<?php echo form_input('area_slug', set_value('area_slug', $result['area_slug'] ?? ''), 'class="slug form-control"'); ?>
				<?php echo form_hidden('old_area_slug', $result['area_slug'] ?? ''); ?>
			</div>
			<div class="mb-3">
				<label class="form-label">Status</label>
				<?php echo form_dropdown('status', ['draft' => 'Draft', 'publish' => 'Publish'], set_value('status', $result['status'] ?? 'publish'), 'class="slug form-control"'); ?>
			</div>

			<div class="border-top pt-3">
				<button type="submit" id="btn-submit-link-form" class="btn btn-primary me-1"><span class="fa fa-save"></span> Save Area</button>
				<a href="<?= site_url('admin/navigation'); ?>" class="btn btn-secondary">Cancel</a>
			</div>
		</div>

	</form>
</div>