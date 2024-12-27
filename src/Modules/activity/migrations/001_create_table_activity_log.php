<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_table_activity_log extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE `activity_logs` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `module` varchar(255) NOT NULL,
                `action` varchar(255) NOT NULL,
                `post_data` text,
                `user_id` int(11) NOT NULL,
                `user_agent` varchar(255) NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NULL DEFAULT NULL,
                `deleted_at` datetime NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");        
    }
    
    public function down()
    {
        $this->db->query("DROP TABLE `activity_logs`");
    }
    
}