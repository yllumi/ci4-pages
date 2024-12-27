<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_field_icon extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mein_navigations`
            CHANGE `parent` `icon` varchar(50) NULL AFTER `status`;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mein_navigations`
            CHANGE `icon` `parent` int(11) NULL AFTER `status`;");
    }

}