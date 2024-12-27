<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_banner_table extends CI_Migration 
{
	public $table = 'banner';

    public function up()
    {
        $this->load->dbforge();
        
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("placing varchar(255) NOT NULL");
        $this->dbforge->add_field("name varchar(255)");
        $this->dbforge->add_field("source varchar(255)");
        $this->dbforge->add_field("status enum('draft','publish','deleted') NOT NULL DEFAULT 'publish'");
        $this->dbforge->add_field("start datetime");
        $this->dbforge->add_field("end datetime");
        $this->dbforge->add_field("client varchar(255)");
        $this->dbforge->add_field("created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at timestamp NULL DEFAULT NULL");
        
        $this->dbforge->create_table($this->table, TRUE, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->dbforge->drop_table($this->table, TRUE);
    }

}