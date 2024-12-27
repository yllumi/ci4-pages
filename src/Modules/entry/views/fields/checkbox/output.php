<?php
$checked = json_decode($result[$config['field']]);
if($checked)
	echo $final_content = implode(", ", $checked);
