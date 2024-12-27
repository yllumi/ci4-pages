<style>
	a.list-group-item {
		color: #333;
	}

	a.list-group-item.disable {
		background: #f3f3f3;
		color: #aaa;
	}
</style>
<?php
$migration_menu = $has_migration;
$core = array_shift($migration_menu);
ksort($migration_menu);
ksort($all_modules);
$segment_5 = $this->uri->segment(5);
$moduleDetail = $all_modules[$segment_5] ?? '';
?>

<div class="row mb-3">
	<div class="col-md-6">
		<h2>Module Management</h2>
	</div>
	<div class="col-md-6 text-end">
		<a href="<?= site_url('admin/development/migration/clear_cache'); ?>" class="btn rounded btn-secondary-outline text-white shadow-sm"><span class="fa fa-refresh"></span> Clear Config Caches</a>
		<a href="<?= site_url('admin/development/migration/migrateAll'); ?>" class="btn bg-white text-dark rounded shadow-sm" onclick="return confirm('Yakin akan migrate semua modul? Ini akan migrate ke versi terakhir dari setiap modulnya.')"><span class="fa fa-cogs"></span> Migrate All Modules</a>
	</div>
</div>

<div class="card rounded-xl shadow">
	<div class="card-body table-responsive">

		<div class="row mt-4 mb-5">
			<div class="col-md-4">
				<div class="modules-list" style="max-height: 500px;overflow-y:auto;background:#eee">
					<div class="list-group">
						<a href="<?= site_url('admin/development/migration'); ?>" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between <?php echo current_url() == site_url('admin/development/migration') ? 'active ' : ' '; ?>">
							Core <span class="badge text-bg-primary rounded-pill"><?= $current[$core[1]]; ?></span>
						</a>
						<?php foreach ($all_modules as $module_menu) : ?>
							<?php
							$moduleUrl = site_url('admin/development/migration/index/' . $module_menu[1]);
							?>
							<a href="<?= $moduleUrl; ?>" class="list-group-item d-flex align-items-center justify-content-between list-group-item-action <?php echo current_url() == $moduleUrl ? 'active ' : ' ';
																																							echo $module_menu['enable'] ? '' : 'disable' ?>">
								<?= $module_menu[1]; ?>
								<?= $module_menu['enable'] ? '' : '<small><em>disabled</em></small>'; ?>
								<?php if (isset($migrations[$module_menu[1]])) : ?>
									<span class="badge rounded-pill <?= $module_menu['enable'] ? 'text-bg-primary' : 'text-secondary'; ?>"><?= $current[$module_menu[1]] . '/' . count($migrations[$module_menu[1]]); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<?php if ($module != 'CI_core') : ?>
					<h3>Module Details</h3>
					<table class="table table-sm mt-3 mb-5">
						<tr>
							<th width="20%">Module Name</th>
							<td><?= $moduleDetail['name']; ?></td>
						</tr>
						<tr>
							<th>Description</th>
							<td><?= $moduleDetail['description']; ?></td>
						</tr>
						<tr>
							<th>Path</th>
							<td><?= $moduleDetail['path']; ?></td>
						</tr>
						<tr>
							<th>Author</th>
							<td><a href="<?= $moduleDetail['author_url']; ?>"><?= $moduleDetail['author']; ?></a></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>
								<div class="d-flex justify-content-between">
									<?php if ($moduleDetail['enable']) : ?>
										<span class="text-success">enabled</span>
										<a href="<?= site_url('admin/development/migration/disable/' . $module); ?>" class="btn btn-sm btn-oval btn-warning">Disable</a>
									<?php else : ?>
										<span class="text-danger">disabled</span>
										<a href="<?= site_url('admin/development/migration/enable/' . $module); ?>" class="btn btn-sm btn-oval btn-info">Enable</a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					</table>
				<?php endif; ?>

				<?php if ($seed ?? '') : ?>
					<h3>Seeder</h3>
					<div class="mt-3 mb-5">
						<div class="d-flex justify-content-between">
							<?php if (!$seed['exists'] && $_ENV['CI_ENV'] != 'production') : ?>
								<span class="text-muted"><span class="fa fa-ban"></span> <?= $seed['file']; ?></span>
								<a href="<?= site_url('admin/development/migration/generateSeeder/' . $module); ?>" class="btn btn-primary btn-sm"><span class="fa fa-pencil-square-o"></span> Create seeder</a>
							<?php else : ?>
								<?php if (!($seed['methods'] ?? '') && $_ENV['CI_ENV'] != 'production') : ?>
									<span class="text-success"><?= $seed['file']; ?></span>
									<a href="<?= site_url('admin/development/migration/runSeeder/' . $module); ?>" class="btn btn-outline-primary btn-sm"><span class="fa fa-cog"></span> Run seeder</a>
								<?php else : ?>
									<table class="table table-sm table-hover">
										<?php foreach ($seed['methods'] as $seedName => $seedConf) : ?>
											<?php if ($seedConf['type'] == 'dummy' && $_ENV['CI_ENV'] == 'production') continue; ?>
											<tr>
												<td><?= $seedName; ?></td>
												<td class="text-end"><a href="<?= site_url('admin/development/migration/runSeeder/' . $module . '/' . $seedConf['method']); ?>" class="btn btn-outline-primary btn-sm"><span class="fa fa-pencil-square-o"></span> Run seeder</a></td>
											</tr>
										<?php endforeach; ?>
									</table>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($migrations[$module] ?? false) : ?>

					<h3>Migrations</h3>
					<div class="card p-2" id="myTabContent">
						<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<table class="table table-bordered table-striped m-0">
								<tr>
									<td width="10%">Version</td>
									<td>File</td>
								</tr>

								<?php if ($current[$module] > 0) : ?>
									<tr>
										<td colspan='3' class="pt-2 pb-1">
											<a class="btn btn-primary <?= (ENVIRONMENT == 'production') ? 'disabled' : '' ?>" href='<?= site_url('admin/development/migration/migrate/' . $module); ?>'>Reset</a>
										</td>
									</tr>
								<?php endif; ?>

								<?php foreach ($migrations[$module] as $migration) : ?>
									<?php
									$filename = basename($migration);
									list($key, $migration_name) = explode('_', $filename, 2);
									?>
									<tr>
										<?php if (intval($key) == $current[$module]) : ?>
											<td><span class="fa fa-check-circle"></span> <?= $key; ?></td>
										<?php else : ?>
											<td>
												<a href='<?= site_url('admin/development/migration/migrate/' . $module . '/' . intval($key)); ?>' class="btn btn-sm btn-primary <?= (ENVIRONMENT == 'production') ? 'disabled' : '' ?>" <?php if (intval($key) < count($migrations[$module])) : ?> onclick="return confirm('Proses rollback akan menghapus data yang sudah ada!\nSerius ini mau dirollback ke versi sebelumnya??')" <?php endif; ?>>
													<?= $key; ?>
												</a>
											</td>
										<?php endif; ?>

										<td><?= $filename; ?></td>
									<tr>
									<?php endforeach; ?>
							</table>
						</div>
					</div>

				<?php endif; ?>

				<?php if (ENVIRONMENT != 'production') : ?>
					<h4 class="py-2 mt-3">Create new migration</h4>
					<div class="d-flex mb-5">
						<div class="col-6 me-1">
							<input type="text" id="migration_name" placeholder="migration_name" class="form-control"> &nbsp;
						</div>
						<div class="col-2">
							<button class="btn btn-primary" onclick="createMigration()" class="p-1">Create</button>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
	function createMigration() {
		let name = document.getElementById('migration_name').value;
		if (name.trim() !== "")
			window.location = '<?= site_url('admin/development/migration/generateMigration'); ?>/' + name + '/<?= $segment_5; ?>';
	}
	$('#modules').change(function() {
		window.location = $(this).val();
	})
	$(function() {
		var scrtop = $(".list-group-item.active").position().top - 40;
		$('.modules-list').animate({
			scrollTop: scrtop
		}, 800);
	})
</script>