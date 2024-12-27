<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_role_slug extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE `mein_roles`
            ADD `role_slug` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `role_name`;");
        
        $this->db->query("UPDATE mein_roles SET role_slug = LOWER(role_name)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mein_roles` DROP `role_slug`;");
    }

}