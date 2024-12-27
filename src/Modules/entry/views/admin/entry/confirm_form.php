<div class="title-block">
 	<h3 class="title">Konfirmasi <?= $action['caption']; ?>
 		<span class="sparkline bar" data-type="bar"></span>
 	</h3>
</div>

<div class="card card-block">

	<form method="post">
		<input type="hidden" name="redirect" value="<?= $redirect; ?>">
		<?php foreach ($fields as $field => $fieldConf): ?>
			
			<div class="form-group">
				<label><?= $fieldConf['label']; ?></label>
				<?= generate_input($fieldConf); ?>
			</div>

		<?php endforeach; ?>

		<button type="submit" class="btn btn-success"><span class="fa fa-check"></span> Confirm</button>
		<a href="<?= site_url("admin/entry/$entry/index/"); ?>" class="btn btn-secondary">Batal</a>
	</form>

</div>