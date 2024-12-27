<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_table_settings extends CI_Migration 
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE `mein_options` (
                `id` int NOT NULL AUTO_INCREMENT,
                `option_group` varchar(30) DEFAULT 'site',
                `option_name` varchar(30) DEFAULT NULL,
                `option_value` text,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $seed = new App\cli\Seed;
        $seed->run('setting');
    }

    public function down()
    {
        $this->dbforge->drop_table('mein_options', TRUE);
    }

}