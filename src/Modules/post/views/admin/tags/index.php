<div class="mb-4">
	<div class="row justify-content-between">
		<div class="col-sm-2 col-md-4">
			<h2 class="mt-1"><?= $page_title; ?></h2>
		</div>
		<div class="col-sm-9 col-md-8 col-xxl-5 text-end">
			<form method="get" action="<?= site_url('admin/post/tags/search'); ?>" enctype="application/x-www-form-urlencoded">
				<div class="input-group">
					<input type="text" class="form-control" name="keyword" value="<?= $keyword ?? ''; ?>" placeholder="Search by title...">
					<button type="submit" class="btn btn-secondary">Search</button>
				</div>
			</form>

			<a href="<?php echo site_url('admin/post/tags/add'); ?>" class="btn btn-primary"><span class="fa fa-plus-circle"></span> New Tags</a>
		</div>
	</div>
</div>

<div class="card rounded-xl shadow py-4">
	<div class="card-body">
		<div class="table-responsive">

			<?php if (empty($results)) : ?>
				<div class="text-center">Belum ada data tag.</div>
			<?php else : ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th><input class="select-all" type="checkbox" /></th>
							<th>Name</th>
							<th>Slug</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

						<?php
						$i = 1;
						foreach ($results as $row) {
						?>

							<tr>
								<td><input id="checkbox_<?php echo $i; ?>" name="record[]" class="record" type="checkbox" value="<?php echo $row->term_id ?>" /></td>
								<td><?php echo $row->name; ?></td>
								<td><?php echo $row->slug; ?></td>
								<td class="text-end">
									<div class="btn-group">
										<a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?php echo site_url('tag/' . $row->slug); ?>"><span class="fa fa-up-right-from-square"></span> Open</a>
										<a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('admin/post/tags/edit/' . $row->term_id); ?>"><span class="fa fa-pencil"></span> Edit</a>
										<a class="btn btn-sm btn-outline-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/post/tags/delete/' . $row->term_id); ?>"><span class="fa fa-trash"></span> Delete</a>
									</div>
								</td>
							</tr>

						<?php
							$i++;
						}
						?>

					</tbody>
				</table>

				<?php if (isset($pagination)) : ?>
					<div class="pagination">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>

			<?php endif ?>

		</div>
	</div>
</div>