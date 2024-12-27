<?php if($result[$config['field']]): ?>
<a href="https://youtu.be/<?= $result[$config['field']]; ?>" target="_blank" class="text-nowrap">
	<?php echo $result[$config['field']]; ?>
	<span class="fa fa-external-link ms-1"></span>
</a>
<?php endif; ?>
