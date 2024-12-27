<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_comment_hashover extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE `comments` (
            `domain` varchar(50) DEFAULT NULL,
            `thread` varchar(255) DEFAULT NULL,
            `comment` text DEFAULT NULL,
            `body` text DEFAULT NULL,
            `status` varchar(10) DEFAULT NULL,
            `date` text DEFAULT NULL,
            `name` text DEFAULT NULL,
            `password` text DEFAULT NULL,
            `login_id` text DEFAULT NULL,
            `email` text DEFAULT NULL,
            `encryption` text DEFAULT NULL,
            `email_hash` text DEFAULT NULL,
            `notifications` text DEFAULT NULL,
            `website` text DEFAULT NULL,
            `ipaddr` text DEFAULT NULL,
            `likes` int(11) DEFAULT NULL,
            `dislikes` int(11) DEFAULT NULL,
            KEY `thread` (`thread`),
            KEY `domain` (`domain`),
            KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        $this->db->query(
            "CREATE TABLE `page-info` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `domain` text DEFAULT NULL,
            `thread` text DEFAULT NULL,
            `url` text DEFAULT NULL,
            `title` text DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    
    public function down()
    {
        $this->dbforge->drop_table('page-info', TRUE);
        $this->dbforge->drop_table('comments', TRUE);
    }

}