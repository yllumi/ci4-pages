<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *	Navigation Shortcode
 *	
 *  Theme api for Navigation feature
 */
class NavigationShortcode extends Shortcode {

	public function area()
	{
        $area = $this->getAttribute('area');

        ci()->load->model(['navigation/Navigation_model','navigation/Nav_area_model']);
        $this->load->helper('navigation/navigation');

        $output = $this->Nav_area_model
                        ->with_navigations('order_inside:nav_order asc')
                        ->where('area_slug', $area)
                        ->where('status', 'publish')
                        ->get();
        
        if(isset($output['navigations']))
            return $this->output(getStructuredNavigation($output['navigations']));
        
        return [];
    }

}