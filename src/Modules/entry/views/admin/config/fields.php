<style>
	.nav-link {
		color: white !important;
		border-radius: 0 !important;
		padding: 10px 20px;
	}

	.nav-link.active {
		color: #333 !important;
	}
</style>

<div class="mb-3">
	<div class="row">
		<div class="col-lg-6">
			<h2><a href="<?= site_url('admin/entry/config'); ?>">Entry Configuration</a> &middot;
				<?= $entryConf['name']; ?>
				<a href="<?= site_url('admin/entry/' . $entry); ?>" target="_blank" title="Open Data"><span class="fa fa-table ms-2"></span></a>
			</h2>
		</div>
	</div>
</div>

<div style="position:relative">
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link" href="<?= site_url('admin/entry/config/configure/' . $entry); ?>">Config</a>
		</li>
		<li class="nav-item">
			<a class="nav-link active" href="<?= site_url('admin/entry/config/fields/' . $entry); ?>">Fields</a>
		</li>
	</ul>

	<div class="card" style="margin-top: 0px;border-top: 0;border-radius: 0;">
		<div class="card-body">
			<div class="border-bottom mb-3 pb-3">
				<button type="button" class="btn btn-outline-primary"><span class="fa fa-plus-circle"></span> Add New Fields</button>
				<button type="button" class="btn btn-outline-secondary"><span class="fa fa-refresh"></span> Sync Table</button>
			</div>

			<div class="row justify-content-between">
				<div class="col-md-5">
					<?php if ($entryConf['fields']) : ?>
						<ul class="list-group">
							<?php foreach ($entryConf['fields'] as $field => $fieldConf) : ?>
								<li class="list-group-item list-group-item-info d-flex justify-content-between">
									<span><?= $fieldConf['label']; ?></span>
									<div>
										<button class="btn btn-sm btn-outline-secondary mb-0"><span class="fa fa-arrow-up"></span></button>
										<button class="btn btn-sm btn-outline-secondary mb-0"><span class="fa fa-arrow-down"></span></button>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>

			<div class="border-top mt-3 pt-3">
				<button type="button" class="btn btn-outline-primary"><span class="fa fa-plus-circle"></span> Add New Fields</button>
				<button type="button" class="btn btn-outline-secondary"><span class="fa fa-refresh"></span> Sync Table</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {

	})
</script>