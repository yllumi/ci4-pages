<?php

class PostSeeder extends App\libraries\Seeder
{
    public function run()
    {
        $this->db->query("INSERT INTO `mein_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
        (1, 1, 'post_category', '', 0, 0);");
        
        $this->db->query("INSERT INTO `mein_terms` (`term_id`, `name`, `slug`) VALUES
        (1, 'Member Updates', 'member-updates');");
    }
}