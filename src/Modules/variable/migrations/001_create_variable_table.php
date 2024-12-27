<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_variable_table extends CI_Migration 
{
	public $table = 'mein_variables';

    public function up()
    {
        $this->load->dbforge();
        
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("variable varchar(100) NOT NULL DEFAULT 'anonymous'");
        $this->dbforge->add_field("value longtext NULL");
        $this->dbforge->add_field("created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at timestamp NULL DEFAULT NULL");
        $this->dbforge->add_field("deleted_at timestamp NULL DEFAULT NULL");

        $this->dbforge->create_table($this->table, TRUE, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->dbforge->drop_table($this->table, TRUE);
    }

}