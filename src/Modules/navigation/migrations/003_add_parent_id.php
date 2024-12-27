<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_parent_id extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mein_navigations`
            ADD `parent_id` int(11) NULL DEFAULT '0' AFTER `nav_order`;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mein_navigations` DROP `parent_id`;");
    }

}