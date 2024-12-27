<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Banner 
 *	
 * Shortcode for Banner Module
 * 
 * @author Oriza
 */
class BannerShortcode extends Shortcode 
{
	public function get()
	{
        $this->load->model('banner/Banner_model');

        $placing = $this->getAttribute('placing');
        $banner = $this->Banner_model->getBanner('placing', $placing);

        if (!empty($banner) && $banner['status'] == 'publish')
        {
            $now = date('Y-m-d h:i:s');
            
            if (($now > $banner['start']) && ($now < $banner['end']))
            {
                return $this->output($banner['source']);
            }
            else
            {
                // Kosong jika expired .. agar tidak mengganggu tampilan
                return '';
            }
        }

		return false;
	}   
}