<style>
	.navigation_list>li:first-child>.navrow .option>.sort-scroll-button-up,
	.navigation_list>li:last-child>.navrow .option>.sort-scroll-button-down {
		visibility: hidden;
	}

	.card.draft {
		opacity: .3;
	}

	.navlist .option .btn {
		margin: 0;
		visibility: visible;
		transition: .1s;
	}

	.navlist ul ul {
		padding-left: 30px;
	}

	.navlist ul li .navrow {
		background-color: #ddd;
		border-left: 5px solid #007bff;
	}

	.navlist ul .navrow:hover {
		background-color: rgba(0, 123, 255, .3);
	}

	.navlist li.draft .navrow {
		opacity: .8;
		background-color: #f3f3f3 !important;
		border-left: 5px solid #aaa;
	}
</style>

<div class="row heading">
	<div class="col-md-6">
		<h2>Navigation</h2>
	</div>
	<div class="col-md-6 text-end">
		<div><a href="<?= site_url('admin/navigation/add_area'); ?>" class="btn btn-secondary"><i class="fa fa-plus fa-fw"></i> Create New Area</a></div>
	</div>
</div>
<br>

<?php if ($areas) : ?>
	<?php foreach ($areas as $area) : ?>
		<div class="card card-info shadow mb-4 <?= $area['status'] == 'draft' ? 'draft' : ''; ?>" id="navarea-<?= $area['id']; ?>">
			<div class="card-header d-flex justify-content-between">
				<p class="title">
					<button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#navlist-<?= $area['id']; ?>" aria-expanded="true" aria-controls="navlist-<?= $area['id']; ?>">
						<span class="fa fa-caret-down"></span>
					</button>
					<?php echo $area['area_name']; ?> &nbsp; <em style="font-weight: 300"><?php echo $area['area_slug']; ?></em> &nbsp;
					<span class="text-danger"><?= $area['status'] == 'draft' ? '(draft)' : ''; ?></span>
				</p>
				<div class="pull-right">
					<a href="<?= site_url('admin/navigation/add_link/' . $area['id']); ?>" class="btn btn-sm btn-primary"><span class="fa fa-link"></span> Add Link</a>
					<a href="<?= site_url('admin/navigation/edit_area/' . $area['id']); ?>" class="btn btn-sm btn-success" title="Edit Area"><span class="fa fa-pencil"></span></a>
					<a href="<?php echo site_url('admin/navigation/delete_area/' . $area['id'] . '/' . $area['area_slug']); ?>" class="btn btn-sm btn-danger remove" title="Delete Area" onclick="return confirm('Yakin akan menghapus area ini?')"><span class="fa fa-times"></span></a>
				</div>
			</div>
			<div class="card-block navlist collapse show" id="navlist-<?= $area['id']; ?>" aria-labelledby="headingOne" data-parent="#navarea-<?= $area['id']; ?>">
				<?php if (isset($area['navigations'])) : ?>
					<div id="<?php echo $area['id']; ?>">
						<ul class="list-unstyled navigation_list">
							<?php echo $this->load->view('admin/navigation_list', ['area' => $area['area_slug'], 'links' => $area['navigations'], 'root' => true], true); ?>
						</ul>
					</div>
				<?php else : ?>
					<p class="align-center m-0"><em>No link yet.</em></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<p><em>No navigation data yet.</em></p>
<?php endif; ?>

<script>
	$(function() {
		$('.field-title').on('keyup', function() {
			let title = $(this).val();
			$('.field-slug').val(title);
		})
	})
</script>