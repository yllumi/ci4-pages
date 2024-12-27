<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_otp_field extends CI_Migration 
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE `mein_users`
            ADD `otp` varchar(6) COLLATE 'utf8mb3_general_ci' NULL AFTER `token`;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mein_users` DROP `otp`");
    }

}