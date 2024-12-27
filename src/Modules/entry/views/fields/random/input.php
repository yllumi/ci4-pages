<?php
$random = random_string($config['mode'] ?? 'alnum', $config['digit'] ?? 8);
if($config['uppercase'] ?? '') $random = strtoupper($random);
if($config['time_prefix'] ?? '') $random = date($config['time_prefix']).$random;
if($config['date_prefix'] ?? '') $random = date($config['date_prefix']).$random;
$value = $value ? $value : $random;
?>

<?php if($config['writable'] ?? ''): ?>
<input type="text" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" name="<?= $config['field'];?>" value="<?= $value; ?>" class="form-control" data-caption="<?= $config['label'];?>"/>
<?php else: ?>
<input type="text" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" name="<?= $config['field'];?>" value="<?= $value; ?>" class="form-control" disabled data-caption="<?= $config['label'];?>"/>
<input type="hidden" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" name="<?= $config['field'];?>" value="<?= $value; ?>" data-caption="<?= $config['label'];?>" />
<?php endif; ?>
