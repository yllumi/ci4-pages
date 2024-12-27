<?php
$random = random_string($config['mode'] ?? 'alnum', $config['digit'] ?? 8);
if($config['uppercase'] ?? '') $random = strtoupper($random);
if($config['time_prefix'] ?? '') $random = date($config['time_prefix']).$random;
if($config['date_prefix'] ?? '') $random = date($config['date_prefix']).$random;
$value = $value ? $value : $random;

echo $value;