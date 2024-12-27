<?php if($config['ellipsis'] ?? true): ?>
    <div style="max-width: 200px" title="<?= $result[$config['field']]; ?>">
        <?= ellipsize($result[$config['field']] ?? '', 20); ?>
    </div>
<?php else: ?>
    <?= $config['nl2br'] ?? false ? nl2br($result[$config['field']] ?? '') : $result[$config['field']]; ?>
<?php endif; ?>