<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Expand_source_fieldtype extends CI_Migration 
{
	public function up()
    {
        $this->db->query("ALTER TABLE `banner` CHANGE `source` `source` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `banner` CHANGE `source` `source` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }

}