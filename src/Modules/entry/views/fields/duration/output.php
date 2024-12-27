<?php
$hms = explode(":", $result[$config['field']]);
$h = $hms[0] ?? 0;
$m = $hms[1] ?? 0;
$s = $hms[2] ?? 0;
?>
<?= $h > 0 ? ltrim($h, '0').' '.t('hour(s)'). ' ' : ''; ?>
<?= $m > 0 ? ltrim($m, '0').' '.t('minute(s)') . ' ' : ''; ?>
<?= $s > 0 ? ltrim($s, '0').' '.t('second(s)') : ''; ?>