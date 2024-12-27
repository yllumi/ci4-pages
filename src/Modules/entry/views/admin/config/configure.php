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
	<h2><a href="<?= site_url('admin/entry/config'); ?>">Entry Configuration</a> &middot;
		<?= $entryConf['name']; ?>
		<a href="<?= site_url('admin/entry/' . $entry); ?>" target="_blank" title="Open Data"><span class="fa fa-table ms-2"></span></a>
	</h2>
</div>

<div style="position:relative">
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" href="<?= site_url('admin/entry/config/configure/' . $entry); ?>">Config</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= site_url('admin/entry/config/fields/' . $entry); ?>">Fields</a>
		</li>
	</ul>

	<div class="card" style="margin-top: 0px;border-top: 0;border-radius: 0;">
		<form method="post">
			<div class="card-body">
				<div class="d-flex border-bottom justify-content-between mb-3 pb-3">
					<div>
						<label>Path:</label> <?= $entryConf['path']; ?>
					</div>
					<div class="text-end">
						<button type="submit" class="btn btn-success rounded"><span class="fa fa-save"></span> Simpan</button>
					</div>
				</div>

				<div class="row justify-content-between">
					<div class="col-md-7 mt-3">
						<?php foreach ($configFields['basicLeftFields'] as $field => $fieldConf) : ?>
							<div class="mb-4">
								<label class="form-label"><?= $fieldConf['label']; ?></label><br>
								<?= $fieldConf['description'] ?? null ? '<small>' . $fieldConf['description'] . '</small>' : ''; ?>
								<?= generate_input($fieldConf, $entryConf[$field] ?? null); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="col-md-4 mt-3">
						<?php foreach ($configFields['basicRightFields'] as $field => $fieldConf) : ?>
							<div class="mb-4">
								<label class="form-label"><?= $fieldConf['label']; ?></label><br>
								<?= $fieldConf['description'] ?? null ? '<small>' . $fieldConf['description'] . '</small>' : ''; ?>
								<?= generate_input($fieldConf, $entryConf[$field] ?? null); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="border-top text-end mt-3 pt-3">
					<button type="button" class="btn btn-success rounded"><span class="fa fa-save"></span> Simpan</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$(function() {

	})
</script>