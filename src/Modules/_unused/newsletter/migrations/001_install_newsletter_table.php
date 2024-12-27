<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_newsletter_table extends CI_Migration 
{
    public function up()
    {
        $this->load->dbforge();
        
        // Create table download member.
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("name varchar(255)");
        $this->dbforge->add_field("email varchar(255)");
        $this->dbforge->add_field("phone varchar(20)");
        $this->dbforge->add_field("token varchar(20)");
        $this->dbforge->add_field("status enum('valid','not valid') NOT NULL DEFAULT 'not valid'");
        $this->dbforge->add_field("created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at timestamp NULL DEFAULT NULL");
        
        $this->dbforge->create_table('newsletter', TRUE, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->dbforge->drop_table('newsletter', TRUE);
    }

}