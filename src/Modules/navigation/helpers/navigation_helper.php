<?php

/**
 * Navigation module helpers 
 */

function getStructuredNavigation($navigations = [], $parent = '0')
{
	if(!$navigations) return [];
	
	$navigations = array_combine(array_column($navigations, 'id'), $navigations);
	
	$filteredByParent = array_filter($navigations, function ($val) use ($parent) {
		return $val['parent_id'] == $parent;
	});

	if($filteredByParent){
		foreach ($filteredByParent as $key => $value) {
			if($children = getStructuredNavigation($navigations, $value['id']))
				$filteredByParent[$key]['children'] = $children;
		}
	}
	
	return $filteredByParent ?? [];
}

function getStructuredNavigationDropdown($structured, $prefix = '')
{
	$output = [];
	foreach ($structured as $id => $nav) {
		$output[$id] = $prefix.' '.$nav['caption'];
		if(isset($nav['children']))
			$output += getStructuredNavigationDropdown($nav['children'], $prefix. '└─');
	}
	return $output;
}