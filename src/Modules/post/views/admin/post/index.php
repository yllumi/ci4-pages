<div class="mb-4">
	<div class="row justify-content-between">
		<div class="col-sm-2 col-md-4">
			<h2 class="mt-1"><?= $page_title; ?></h2>
		</div>
		<div class="col-sm-10 col-md-8 col-xxl-5 text-end">
			<form method="get" action="<?= site_url('admin/post/search'); ?>" enctype="application/x-www-form-urlencoded">
				<input type="hidden" name="status" value="<?= $status; ?>" />
				<input type="hidden" name="type" value="<?= $type; ?>" />
				<div class="input-group">
					<input type="text" class="form-control" name="keyword" value="<?= $keyword ?? ''; ?>" placeholder="Search by title...">
					<button type="submit" class="btn btn-secondary">Search</button>
				</div>
			</form>

			<div class="btn-group mb-2">
				<a class="btn <?= ci()->uri->segment(4) == 'all' ? 'btn-info' : 'btn-secondary'; ?>" href="<?= site_url('admin/post/index/all/' . $type); ?>">All</a>
				<a class="btn <?= ci()->uri->segment(4) == 'draft' ? 'btn-info' : 'btn-secondary'; ?>" href="<?= site_url('admin/post/index/draft/' . $type); ?>">Draft</a>
				<a class="btn <?= ci()->uri->segment(4) == 'review' ? 'btn-info' : 'btn-secondary'; ?>" href="<?= site_url('admin/post/index/review/' . $type); ?>">Review</a>
				<a class="btn <?= ci()->uri->segment(4) == 'publish' ? 'btn-info' : 'btn-secondary'; ?>" href="<?= site_url('admin/post/index/publish/' . $type); ?>">Publish</a>
				<a class="btn <?= ci()->uri->segment(4) == 'trash' ? 'btn-info' : 'btn-secondary'; ?>" href="<?= site_url('admin/post/index/trash/' . $type); ?>">Trash</a>
			</div>

			<a href="<?= site_url('admin/post/add/' . $type) ?>" class="btn btn-primary ms-2 mb-2"><span class="fa fa-plus-circle"></span> New <?= ucfirst($type); ?></a>
		</div>
	</div>
</div>



<div class="card rounded-xl shadow pb-4 mb-4">
	<div class="card-body">
		<div class="table-responsive">

			<?php if (empty($results)) : ?>
				<div class="text-center">Belum ada data.</div>
			<?php else : ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Title</th>
							<th>Category</th>
							<th>Video</th>
							<th>Status</th>

							<?php if ($type == 'all') : ?>
								<th>Created</th>
							<?php endif; ?>

							<th>Created</th>
							<th>Published</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

						<?php
						$i = 1;

						foreach ($results as $row) {
							$category = $this->Taxonomy_model->get_category($row->id);
						?>

							<tr>
								<td>
									<img src="<?= empty($row->embed_video) ? $row->featured_image : 'https://img.youtube.com/vi/' . $row->embed_video . '/mqdefault.jpg'; ?>" style="object-fit:cover;width:50px;height:50px;" alt="">
								</td>
								<td>
									<?php if (isset($search_mode) && $search_mode == true) : ?>

										<div>
											<?= str_replace($keyword, '<b>' . $keyword . '</b>', $row->title); ?>
										</div>

									<?php else : ?>

										<div>
											<a href="<?= site_url($row->slug); ?>" target="_blank"><?= $row->title; ?></a><br>
											<small><?= $row->slug; ?></small>
										</div>

									<?php endif; ?>

								</td>

								<td><?= (isset($category->name)) ? $category->name : '-'; ?></td>
								<td><?= $row->embed_video; ?></td>
								<td>
									<div>
										<?php
										if ($row->status == 'publish')
											echo '<span class="badge rounded-pill text-bg-success">Published</span>';
										else if ($row->status == 'draft')
											echo '<span class="badge rounded-pill text-bg-danger">Draft</span>';
										else if ($row->status == 'review')
											echo '<span class="badge rounded-pill text-bg-warning">On Review</span>';
										else
											echo '<span class="badge rounded-pill text-bg-info">' . ucfirst($row->status) . '</span>';
										?>
									</div>
								</td>

								<?php if ($type == 'all') : ?>
									<td><a href="<?= base_url('admin/post/index/all/' . $row->type); ?>"><?= ucfirst(str_replace('_', ' ', $row->type)); ?></a></td>
								<?php endif; ?>

								<td>
									<div><?= PHP81_BC\strftime("%d %B %Y, %H:%M", strtotime($row->created_at), config_item('locale')); ?></div>
								</td>
								<td>
									<div><?= ($row->published_at == NULL) ? '-' : PHP81_BC\strftime("%d %B %Y, %H:%M", strtotime($row->published_at), config_item('locale')); ?></div>
								</td>

								<td class="text-end">
									<div class="btn-group">
										<?php if ($row->status == 'trash') : ?>
											<a class="btn btn-sm btn-outline-warning" onclick="return confirm('Sure?')" href="<?= site_url('admin/post/restore/' . $row->id . '?callback=' . current_url()); ?>"><i class="fa fa-check"></i> Restore</a>
											<a class="btn btn-sm btn-outline-danger" onclick="return confirm('Sure?')" href="<?= site_url('admin/post/delete/' . $row->id . '?callback=' . current_url()); ?>"><i class="fa fa-remove"></i> Delete</a>
										<?php else : ?>
											<a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/post/edit/' . $row->id); ?>">
												<i class="fa fa-pencil"></i> Edit
											</a>
											<a class="btn btn-sm btn-outline-danger" onclick="return confirm('Sure?')" href="<?= site_url('admin/post/trash/' . $row->id . '?callback=' . current_url()); ?>">
												<i class="fa fa-trash"></i> Trash
											</a>
										<?php endif ?>
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
						<?= $pagination; ?>
					</div>
				<?php endif; ?>

			<?php endif ?>

		</div>
	</div>
</div>