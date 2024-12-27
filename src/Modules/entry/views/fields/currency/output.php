<div class="text-end">
    <span class="<?= ($config['streak_text'] ?? false) == true ? 'del' : ''; ?>">Rp&nbsp;<?php echo number_format($result[$config['field']], 0, ',', '.') . ',-'; ?></span>
</div>