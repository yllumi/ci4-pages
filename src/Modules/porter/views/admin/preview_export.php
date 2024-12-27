<div class="mb-3">
	<div class="row">
		<div class="col-lg-6">
			<h2><?php echo $page_title; ?></h2>
			<p class="lead text-white"><?= $export['description']; ?></p>
		</div>
		<div class="col-lg-6 text-end">
			<a class="btn bg-white" href="<?= site_url('admin/entry/exporter/action/row/download/'. $export['id']); ?>"><span class="fa fa-download"></span> Unduh data</a>
		</div>
	</div>
</div>

<?php echo $this->session->flashdata('message'); ?>

<div class="card">
	<div class="card-body">
		<?php
		$partial = $total >= 10 ? 10 : $total;
		?>
		<p class="lead">Menampilkan <?= $partial; ?> dari total <?= $total; ?> baris: </p>

		<div class="table-responsive">
			<table class="table table-sm">
				<thead>
					<tr>
						<th></th>
						<?php foreach ($header as $field) : ?>
							<th><?= $field; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1;
					while ($row = $result->unbuffered_row('array')) : ?>
						<tr>
							<th><?= $i; ?></th>
							<?php foreach ($row as $field => $value) : ?>
								<td><?= $value; ?></td>
							<?php endforeach; ?>
						</tr>
					<?php $i++; if($i > 10) break; ?>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>