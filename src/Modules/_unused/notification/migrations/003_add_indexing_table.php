<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_indexing_table extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `notifs` ADD INDEX `is_global` (`is_global`), ADD INDEX `created_at` (`created_at`);");
        $this->db->query("ALTER TABLE `notif_recipients` ADD INDEX `user_id` (`user_id`);");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `notifs` DROP INDEX `is_global`, DROP INDEX `created_at`;");
        $this->db->query("ALTER TABLE `notif_recipients` DROP INDEX `user_id`;");
    }

}