<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Move_phone_to_user extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE mein_users
            ADD phone varchar(15) COLLATE 'utf8_general_ci' NULL AFTER email;");
        
        $this->db->query("UPDATE mein_users set phone = 
            (SELECT phone
            FROM mein_user_profile
            WHERE user_id = mein_users.id)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE mein_users DROP phone;");
    }

}