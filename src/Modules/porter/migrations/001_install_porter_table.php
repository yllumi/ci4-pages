<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_porter_table extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE `mein_exporter` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) DEFAULT NULL,
            `slug` varchar(255) DEFAULT NULL,
            `description` text DEFAULT NULL,
            `query` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        
        $this->db->query(
            "CREATE TABLE `mein_importer` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) DEFAULT NULL,
            `slug` varchar(255) DEFAULT NULL,
            `description` text DEFAULT NULL,
            `schema` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        $this->dbforge->drop_table('mein_importer', TRUE);
        $this->dbforge->drop_table('mein_exporter', TRUE);
    }
}