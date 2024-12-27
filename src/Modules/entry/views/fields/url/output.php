<?php if($result[$config['field']]): ?>
<a href="<?= $result[$config['field']]; ?>" target="_blank">
	<?php if($config['show_url'] ?? ''): ?>
	<span class="me-2"><?= $result[$config['field']]; ?></span>
	<?php endif; ?>
	<span class="fa fa-external-link"></span>
</a>
<?php endif; ?>