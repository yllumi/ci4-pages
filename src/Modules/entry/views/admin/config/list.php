<style>
	input:checked+.slider.disabled {
		background-color: #a8d9ff;
	}

	.nav-tabs .nav-link,
	.nav-tabs .nav-link:hover {
		color: white;
	}

	.nav-tabs .nav-link.active:hover {
		color: black;
	}
</style>

<div class="mb-3">
	<div class="row">
		<div class="col-lg-6">
			<h2><?php echo $page_title; ?></h2>
		</div>
	</div>
</div>

<?php if (empty($results)) : ?>
	<p>No record found ..</p>
<?php else : ?>

	<?php echo $this->session->flashdata('message'); ?>

	<!-- STANDALONE ENTRIES -->
	<div class="card rounded-xl shadow">
		<div class="card-body table-responsive">

			<h4>Standalone Entries</h4>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Entry Name</th>
						<th class="text-center">Enable</th>
						<th class="text-center">Table Exist</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($standalone as $entry) :
						$entryConf = $results[$entry];
						if (setting_item('entry.show_disabled_entries') == 0 && $entryConf['enable'] == false) continue;
						$table_exists = $this->db->table_exists($entryConf['table']);
						$view_table_exists = isset($entryConf['view_table'])
							&& $this->db->table_exists($entryConf['view_table'])
							? true : false;
					?>
						<tr>
							<td>
								<a href="<?= site_url("admin/entry/{$entry}/index/"); ?>" class="lead <?= !$table_exists ? 'disabled' : 'text-info'; ?>" target="_blank"><span class="fa fa-list"></span> <?= $entry; ?></a>
								<br>
								<small class="text-muted"><?= $entryConf['description'] ?? ''; ?></small>
							</td>
							<td class="text-center">
								<label class=" align-middle switch">
									<input type="checkbox" <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?> id="check_status_<?= $entry; ?>" <?= $entryConf['enable'] ? 'checked' : ''; ?> onclick="updateStatusEnable('<?= $entry; ?>')">
									<span class="slider round d-inline-block <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"></span>
									<input type="hidden" id="status_<?= $entry; ?>" value="<?= $entryConf['enable']; ?>">
								</label>
							</td>
							<td class="text-nowrap text-center">
								<?= $table_exists
									? '<span class="fa fa-check-circle text-success lead me-2"></span>'
									: '<span class="fa fa-ban text-warning me-2"></span>'; ?>
							</td>
							<td class="text-end">
								<a href="<?= site_url('admin/entry/config/configure/' . $entry); ?>" class="btn btn-sm btn-outline-primary"><span class="fa fa-cog"></span> Configure</a>

								<?php if (!$table_exists) : ?>
									<a href="<?= site_url('admin/entry/config/sync/' . $entry); ?>" class="btn btn-sm btn-outline-success <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-plug"></span> Build Table</a>
								<?php else : ?>
									<a href="<?= site_url('admin/entry/config/sync/' . $entry); ?>" class="btn btn-sm btn-outline-secondary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-refresh"></span> Sync Table</a>
								<?php endif; ?>

								<!-- view_query -->
								<?php if (isset($entryConf['view_query'])) : ?>
									<?php if (!$view_table_exists) : ?>
										<a href="<?= site_url('admin/entry/config/create_view/' . $entry); ?>" class="btn btn-sm btn-outline-primary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-plug"></span> Build View</a>
									<?php else : ?>
										<a href="<?= site_url('admin/entry/config/create_view/' . $entry); ?>" class="btn btn-sm btn-outline-secondary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-refresh"></span> Update View</a>
									<?php endif; ?>
								<?php elseif (isset($entryConf['view_table'])) : ?>
									<small class="text-muted">View query not defined</small>
								<?php endif; ?>
							</td>
						</tr>

					<?php endforeach; ?>

				</tbody>
			</table>
		</div>
	</div>

	<!-- MODULES ENTRIES -->
	<div class="card rounded-xl shadow mt-2">
		<div class="card-body table-responsive">

			<h4>Module Entries</h4>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Entry Name</th>
						<th class="text-center">Enable</th>
						<th class="text-center">Table Exist</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($hierarchy as $module => $entries) : ?>
						<tr>
							<th colspan="4" class="bg-grey text-info"><?= $module == 'standalone' ? 'Standalone Entries' : ucwords(str_replace("_", " ", $module)) . " Module Entries"; ?></th>
						</tr>

						<?php
						foreach ($entries as $entry) :
							$entryConf = $results[$entry];
							if (setting_item('entry.show_disabled_entries') == 0 && $entryConf['enable'] == false) continue;
							$table_exists = $this->db->table_exists($entryConf['table']);
							$view_table_exists = isset($entryConf['view_table'])
								&& $this->db->table_exists($entryConf['view_table'])
								? true : false;
						?>
							<tr>
								<td>
									<a href="<?= !$table_exists ? '' : site_url("admin/entry/{$entry}/index/"); ?>" class="lead <?= !$table_exists ? 'disabled' : 'text-info'; ?>" target="_blank"><span class="fa fa-list"></span> <?= $entry; ?> </a>
									<br>
									<small class="text-muted"><?= $entryConf['description'] ?? ''; ?></small>
								</td>
								<td class="text-center">
									<label class=" align-middle switch">
										<input type="checkbox" <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?> id="check_status_<?= $entry; ?>" <?= $entryConf['enable'] ? 'checked' : ''; ?> onclick="updateStatusEnable('<?= $entry; ?>')">
										<span class="slider round d-inline-block <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"></span>
										<input type="hidden" id="status_<?= $entry; ?>" value="<?= $entryConf['enable']; ?>">
									</label>
								</td>
								<td class="text-nowrap text-center">
									<?= $table_exists
										? '<span class="fa fa-check-circle text-success lead me-2"></span>'
										: '<span class="fa fa-ban text-warning me-2"></span>'; ?>
								</td>
								<td class="text-end">
									<!-- View Query -->
									<?php if (isset($entryConf['view_query'])) : ?>
										<?php if (!$view_table_exists) : ?>
											<a href="<?= site_url('admin/entry/config/create_view/' . $entry); ?>" class="btn btn-sm btn-outline-primary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-plug"></span> Build View</a>
										<?php else : ?>
											<a href="<?= site_url('admin/entry/config/create_view/' . $entry); ?>" class="btn btn-sm btn-outline-secondary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-refresh"></span> Update View</a>
										<?php endif; ?>
									<?php elseif (isset($entryConf['view_table'])) : ?>
										<small class="text-danger me-2">View table used but query not defined</small>
									<?php endif; ?>

									<a href="<?= site_url('admin/entry/config/configure/' . $entry); ?>" class="btn btn-sm btn-outline-primary"><span class="fa fa-cog"></span> Configure</a>

									<?php if (!$table_exists) : ?>
										<a href="<?= site_url('admin/entry/config/sync/' . $entry); ?>" class="btn btn-sm btn-outline-success <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-plug"></span> Build Table</a>
									<?php else : ?>
										<a href="<?= site_url('admin/entry/config/sync/' . $entry); ?>" class="btn btn-sm btn-outline-secondary <?= !isPermitted('enable', 'entry') ? 'disabled' : ''; ?>"><span class="fa fa-refresh"></span> Sync Table</a>
									<?php endif; ?>
								</td>
							</tr>

						<?php endforeach; ?>
					<?php endforeach; ?>

				</tbody>
			</table>

		</div>
	</div>

<?php endif ?>

<div class="modal fade" id="createEntryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Create New Entry</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="<?= site_url('admin/entry/config/init_entry/'); ?>" method="post">
				<div class="modal-body">
					<label>Entry Name</label>
					<input type="text" name="entry_name" required class="form-control">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Create Entry</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
	function updateStatusEnable(entry) {
		var val = $('#status_' + entry).val();
		if (val == 1)
			window.location.href = '<?= site_url('admin/entry/config/disable'); ?>/' + entry;
		else
			window.location.href = '<?= site_url('admin/entry/config/enable'); ?>/' + entry;
	}
</script>