<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_navigation_table extends CI_Migration 
{
    public $nav_table = 'mein_navigations';
	public $area_table = 'mein_navigation_areas';

    public function up()
    {
        $this->load->dbforge();
        
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("area_id int NOT NULL");
        $this->dbforge->add_field("caption varchar(100) NOT NULL");
        $this->dbforge->add_field("url text");
        $this->dbforge->add_field("url_type enum('uri','external') NOT NULL DEFAULT 'uri'");
        $this->dbforge->add_field("target enum('_blank','_self','_top','_parent') DEFAULT '_self'");
        $this->dbforge->add_field("status enum('draft','publish') NOT NULL DEFAULT 'publish'");
        $this->dbforge->add_field("parent int NULL");
        $this->dbforge->add_field("nav_order int NOT NULL DEFAULT 0");
        $this->dbforge->add_field("created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at timestamp NULL DEFAULT NULL");
        $this->dbforge->add_field("deleted_at timestamp NULL DEFAULT NULL");

        $this->dbforge->create_table($this->nav_table, TRUE, array('ENGINE' => 'InnoDB'));

        $this->dbforge->add_field("id");
        $this->dbforge->add_field("area_name varchar(100) NOT NULL");
        $this->dbforge->add_field("area_slug varchar(100) NOT NULL");
        $this->dbforge->add_field("status enum('draft','publish') NOT NULL DEFAULT 'publish'");
        $this->dbforge->add_field("created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP");
        $this->dbforge->add_field("updated_at timestamp NULL DEFAULT NULL");
        $this->dbforge->add_field("deleted_at timestamp NULL DEFAULT NULL");

        $this->dbforge->create_table($this->area_table, TRUE, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->dbforge->drop_table($this->nav_table, TRUE);
        $this->dbforge->drop_table($this->area_table, TRUE);
    }

}