<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *	User Shortcode
 *	
 *  Theme api for User feature
 */
class UserShortcode extends Shortcode {

	public function __construct()
	{
		parent::__construct();
    }

    /**
	 * Get User Detail
	 */
    public function getUser()
	{
        $id = $this->getAttribute('id');
        
        $user = $this->ci_auth->getUser('id', $id);
        
        return $user;
    }

    /**
	 * Get Profile Picture
	 */
    public function getProfilePicture()
	{
        $filename = $this->getAttribute('filename', null);
        return $this->ci_auth->getProfilePicture($filename);
    }
}