<?php
$phone = substr($result[$config['field']] ?? '', 0, 1)=='0' 
        ? substr_replace($result[$config['field']], '62', 0, 1) 
        : $result[$config['field']];
?>
<a href="https://wa.me/<?= $phone; ?>" target="_blank"><?= $phone; ?></a>