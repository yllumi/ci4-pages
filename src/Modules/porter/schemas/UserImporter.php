<?php namespace App\modules\porter\schemas;

class UserImporter {

    public function __construct()
    {
        // Get roles
        $rolesData = ci()->db->get('mein_roles')->result_array();
        $this->roles = array_combine(array_column($rolesData, 'role_slug'), array_column($rolesData, 'id'));
    }

    public function generateUsername($string)
    {
        return empty(trim($string)) ? random_string('alnum', 12) : $string;
    }
    
    public function generatePassword($string)
    {
        ci()->load->library('user/phpass');
        return ci()->phpass->HashPassword($string);
    }
    
    public function getRoleId($role)
    {
        return $this->roles[$role];
    }

}