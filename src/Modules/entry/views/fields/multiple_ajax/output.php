<?php 
// Mutation.
if (isset($config['relation']))
{
	$field = $config['field'];
	$config['field'] = $config['relation']['caption'];
	
	if(!empty($result[$field])){
		$value = [];
		foreach ($result[$field] as $resId => $res)
			foreach ($config['field'] as $caption)
				$value[$resId][] = $res[$caption];
		
		foreach ($value as $val)
			echo "<span class='badge badge-secondary'>".implode(' - ', $val)."</span>&nbsp;";
	}
}
else 
{
    echo $config['options'][$result[$config['field']]];
}