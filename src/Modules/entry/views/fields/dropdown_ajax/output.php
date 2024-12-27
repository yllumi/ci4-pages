<?php 
// Mutation.
if (isset($config['relation']))
{
	$field = $config['field'];
	$entry = $config['relation']['entry'];
	$caption = $config['relation']['caption'];

	if(! ($result[$entry] ?? '')) {
		echo "-";
		return;
	}
	
	$output = '';
	if(is_array($caption)){
    	foreach ($caption as $capt)
			$output .= $result[$entry][$capt] ?? $capt;
    } else {
		$output = $result[$entry][$caption] ?? '';
    }

	$linkTo = $config['relation']['link_to'] ?? '';
	$linkTarget = $config['relation']['link_target'] ?? '_self';
	echo $linkTo
		? '<a href="'. site_url($linkTo) . $result[$entry]['id'] .'" target="'.$linkTarget.'">'.$output.'</a>'
		: $output;
}