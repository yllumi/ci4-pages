<style>
	.dropdown-item.active {
		color: white !important;
	}
</style>
<form action="<?= site_url('admin/setting/update/' . $current_module) ?>" method="post">

	<?php
	$core_setting_sorted = array_values($core_setting);
	usort($core_setting_sorted, function ($a, $b) {
		return strcmp($a["menu_position"], $b["menu_position"]);
	});
	?>
	<div class="dropright me-3 d-block d-md-none">
		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><span class="fa fa-list"></span></button>
		<div class="dropdown-menu shadow-lg" aria-labelledby="dropdownMenuButton">
			<strong class="font-weight-bold dropdown-item">Core Settings</strong>
			<?php
			foreach ($core_setting_sorted as $module) : ?>
				<?php if (!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) continue; ?>
				<a class="dropdown-item py-1 <?= $module['slug'] == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module['slug']); ?>"><?= $module['name']; ?></a>
			<?php endforeach; ?>
			<div class="dropdown-divider"></div>

			<?php if ($site_setting) : ?>
				<strong class="font-weight-bold dropdown-item">Site Settings</strong>
				<?php foreach ($site_setting as $module_slug => $module) : ?>
					<?php if (!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) continue; ?>
					<a class="dropdown-item py-1 <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
				<?php endforeach; ?>
				<div class="dropdown-divider"></div>
			<?php endif; ?>

			<strong class="font-weight-bold dropdown-item">Module Settings</strong>
			<?php foreach ($modules_setting as $module_slug => $module) : ?>
				<?php if ((!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) || !$module['enable']) continue; ?>
				<a class="dropdown-item py-1 <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
			<?php endforeach; ?>

			<?php foreach ($entries_setting as $module_slug => $module) : ?>
				<?php if ((!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) || !$module['enable']) continue; ?>
				<a class="dropdown-item py-1 <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
			<?php endforeach; ?>

		</div>
	</div>

	<div class="row">
		<div class="nav col-lg-2 col-md-3 col-5 flex-column nav-pills mb-5 ps-2 d-none d-md-flex" id="v-pills-tab" role="tablist" aria-orientation="vertical">
			<div class="card rounded-xl shadow">
				<div class="card-body">

					<h6 class="font-weight-bold text-info p-2 mb-0 mt-2 border-top border-bottom">Core Settings</h6>
					<?php
					foreach ($core_setting_sorted as $module) : ?>
						<?php if (!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) continue; ?>
						<a class="nav-link <?= $module['slug'] == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module['slug']); ?>"><?= $module['name']; ?></a>
					<?php endforeach; ?>

					<?php if ($site_setting) : ?>
						<h6 class="font-weight-bold text-info p-2 mb-0 mt-2 border-top border-bottom">Site Settings</h6>
						<?php foreach ($site_setting as $module_slug => $module) : ?>
							<?php if (!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) continue; ?>
							<a class="nav-link <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
						<?php endforeach; ?>
					<?php endif; ?>

					<h6 class="font-weight-bold text-info p-2 mb-0 mt-2 border-top border-bottom">Module Settings</h6>
					<?php foreach ($modules_setting as $module_slug => $module) : ?>
						<?php if ((!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) || !$module['enable']) continue; ?>
						<a class="nav-link <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
					<?php endforeach; ?>

					<?php foreach ($entries_setting as $module_slug => $module) : ?>
						<?php if ((!isPermitted('show_dev_fields', 'setting') && ($module['dev_purpose'] ?? false) == true) || !$module['enable']) continue; ?>
						<a class="nav-link <?= $module_slug == $current_module ? 'active' : ''; ?>" href="<?= site_url('admin/setting/index/' . $module_slug); ?>" role="tab" aria-controls="v-pills-<?= $module_slug; ?>" aria-selected="false"><?= $module['name']; ?></a>
					<?php endforeach; ?>

				</div>
			</div>
		</div>

		<div class="col-lg-10 col-md-9 col-12 tab-content px-3" id="v-pills-tabContent">
			<div class="card rounded-xl shadow mb-5 p-2">
				<div class="card-body">

					<div class="row justify-content-between mb-4">
						<div class="col-md-8 d-flex justify-content-begin mb-4">
							<h2 class="text-dark"><?= $page_title; ?></h2>
						</div>
						<div class="col-md-4 text-end mb-4">
							<button type="submit" class="btn btn-lg btn-success rounded shadow-sm"><span class="fa fa-save"></span> Save Settings</button>
						</div>
					</div>

					<div class="tab-pane active" role="tabpanel">
						<?php foreach ($current_setting['setting'] as $setting) : ?>
							<?php if (!isPermitted('show_dev_fields', 'setting') && ($setting['dev_purpose'] ?? false) == true) continue; ?>
							<div class="mb-3">
								<?php if ($setting['dev_purpose'] ?? false) : ?>
									<label class="form-label me-2 text-success">
										<span class="fa fa-cog" title="Developer setting only"></span>
									<?php else : ?>
										<label class="form-label me-2">
										<?php endif; ?>
										<?= $setting['label'] ?? make_label($setting['field']); ?>
										</label>
										<code><?= $current_module . '.' . $setting['field']; ?></code>
										<?php if ($setting['desc'] ?? '') : ?>
											<p class="small"><?= $setting['desc'] ?? ''; ?></p>
										<?php endif; ?>

										<div class="input-group">
											<?php
											$config = array_merge($setting, ['field' => $current_module . '[' . $setting['field'] . ']']);
											echo generate_input($config, setting_item($current_module . '.' . $setting['field']));
											?>
										</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="text-end mt-5 mb-2">
						<button type="submit" class="btn btn-lg btn-success rounded shadow-sm"><span class="fa fa-save"></span> Save Settings</button>
					</div>

				</div>
			</div>
		</div>

	</div>

</form>