<span class="text-nowrap">
<?php if(($result[$config['field']] ?? '0') == '0'): ?>
		<span title="<?= $config['options'][$result[$config['field']]  ?? $config['default']];?>" class="fa fa-times-circle text-warning"></span> <?= $config['show_label'] ?? '' ? $config['options'][$result[$config['field']] ?? $config['default']] : '';?>
<?php else: ?>
	<span title="<?= $config['options'][$result[$config['field']] ?? $config['default']];?>" class="fa fa-check-circle text-success"></span> <?= $config['show_label'] ?? '' ? $config['options'][$result[$config['field']] ?? $config['default']] : '';?>
	<?php endif; ?>
</span>
			