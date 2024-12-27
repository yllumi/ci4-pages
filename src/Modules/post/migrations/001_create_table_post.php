<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_table_post extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE `mein_posts` (
                `id` int NOT NULL AUTO_INCREMENT,
                `author` bigint unsigned NOT NULL DEFAULT '0',
                `content` longtext NOT NULL,
                `content_type` varchar(20) NOT NULL DEFAULT 'markdown',
                `intro` text,
                `featured` datetime DEFAULT NULL,
                `title` text NOT NULL,
                `status` varchar(20) NOT NULL DEFAULT 'publish',
                `slug` varchar(200) NOT NULL DEFAULT '',
                `total_seen` int NOT NULL,
                `type` varchar(20) NOT NULL DEFAULT 'post',
                `template` varchar(30) NOT NULL,
                `featured_image` varchar(200) NOT NULL,
                `embed_video` varchar(255) DEFAULT NULL,
                `published_at` datetime DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `featured` (`featured`,`status`,`type`),
                KEY `author` (`author`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            
        $this->db->query(
            "CREATE TABLE `mein_term_relationships` (
                `id` int NOT NULL AUTO_INCREMENT,
                `object_id` bigint unsigned NOT NULL DEFAULT '0',
                `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
                `term_order` int NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query(
            "CREATE TABLE `mein_term_taxonomy` (
                `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `term_id` bigint unsigned NOT NULL DEFAULT '0',
                `taxonomy` varchar(32) NOT NULL DEFAULT '',
                `description` longtext NOT NULL,
                `parent` bigint unsigned NOT NULL DEFAULT '0',
                `count` bigint NOT NULL DEFAULT '0',
                PRIMARY KEY (`term_taxonomy_id`),
                KEY `term_id` (`term_id`,`taxonomy`,`parent`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query(
            "CREATE TABLE `mein_terms` (
                `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(200) NOT NULL DEFAULT '',
                `slug` varchar(200) NOT NULL DEFAULT '',
                PRIMARY KEY (`term_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Run seeds
        $seed = new App\cli\Seed;
        $seed->run('post');
    }

    public function down()
    {
        $this->dbforge->drop_table('mein_posts', TRUE);
        $this->dbforge->drop_table('mein_terms', TRUE);
        $this->dbforge->drop_table('mein_term_taxonomy', TRUE);
        $this->dbforge->drop_table('mein_term_relationships', TRUE);
    }

}