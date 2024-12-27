<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_video_duration extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mein_posts` ADD `video_duration` varchar(20) NULL AFTER `embed_video`;");
    }
    
    public function down()
    {
        $this->db->query("ALTER TABLE `mein_posts` DROP `video_duration`;");
    }

}