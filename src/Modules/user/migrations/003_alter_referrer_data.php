<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Alter_referrer_data extends CI_Migration 
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mein_users`
            CHANGE `referral_code` `referrer_code` varchar(50) COLLATE 'utf8mb3_general_ci' NULL AFTER `url`,
            DROP `referrer_id`;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mein_users`
            CHANGE `referrer_code` `referral_code` varchar(50) COLLATE 'utf8mb3_general_ci' NULL AFTER `url`,
            ADD `referrer_id` int NULL AFTER `referral_code`;");
    }

}