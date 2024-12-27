<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_notification_table extends CI_Migration 
{
    public function up()
    {
        $this->db->query("CREATE TABLE `notifs` (
          `id` bigint(20) UNSIGNED NOT NULL,
          `notif_type` varchar(20) NOT NULL,
          `notif_meta` text NOT NULL,
          `hash` varchar(255) NOT NULL,
          `is_backend` tinyint(1) NOT NULL DEFAULT '0',
          `is_global` tinyint(1) NOT NULL DEFAULT '0',
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL,
          `deleted_at` timestamp NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("ALTER TABLE `notifs` ADD PRIMARY KEY (`id`);");
        $this->db->query("ALTER TABLE `notifs` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;");

        $this->db->query("CREATE TABLE `notif_recipients` (
          `id` bigint(20) UNSIGNED NOT NULL,
          `notif_id` bigint(20) UNSIGNED NOT NULL,
          `user_id` int(11) NOT NULL,
          `read` tinyint(1) NOT NULL DEFAULT '0',
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        
        $this->db->query("ALTER TABLE `notif_recipients` ADD PRIMARY KEY (`id`);");
        $this->db->query("ALTER TABLE `notif_recipients` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;");
    }

    public function down()
    {
        $this->dbforge->drop_table('notif_recipients', TRUE);
        $this->dbforge->drop_table('notifs', TRUE);
    }

}