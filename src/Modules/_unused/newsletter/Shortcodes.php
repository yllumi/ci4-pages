<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *	Newsletter Shortcode
 *	
 *  Theme api for Newsletter feature
 */
class NewsletterShortcode extends Shortcode {

	public function __construct()
	{
		parent::__construct();
        
		$this->load->model('newsletter/Newsletter_model');
    }
    
    public function form()
	{
		$this->load->library('Recaptcha');
        
		$button_label = $this->getAttribute('button_label', 'Send');
        $button_class = $this->getAttribute('button_class', 'btn btn-success');
        $success_message = $this->getAttribute('success_message', '<div class="alert alert-success">Done!</div>');
        
        $this->session->set_userdata('success_message', $success_message);

		$widget = $this->recaptcha->getWidget();
        $script = $this->recaptcha->getScriptTag();
		
		$output  = $script;
		
		$output .= form_open(site_url('newsletter/send_email'), ['id' => 'form-newsletter', 'method' => 'post', 'enctype' => 'multipart/form-data']);
        $output .= '<div>' . $this->session->flashdata('message') . '</div>';
		
		
        $output .= '<div class="form-group">';
        $output .= '<label>Name</label>';
        $output .= '<input type="text" name="name" class="form-control" placeholder="Write your name .." required/>';
		$output .= '</div>';
		
        
        $output .= '<div class="form-group">';
        $output .= '<label>Email</label>';
        $output .= '<input type="text" name="email" class="form-control" placeholder="Write your email .." required/>';
		$output .= '</div>';

		$output .= '<div class="form-group">';
        $output .= $widget;
        $output .= '</div>';
		
        $output .= '<button type="submit" class="'. $button_class .'">'. $button_label .'</button>';
        $output .= form_close();

		return $this->output($output);
    }
}