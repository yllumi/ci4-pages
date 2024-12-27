<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_last_seen extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mein_posts` ADD `last_seen` datetime NOT NULL AFTER `total_seen`;");
    }
    
    public function down()
    {
        $this->db->query("ALTER TABLE `mein_posts` DROP `last_seen`;");
    }

}