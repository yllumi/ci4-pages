<?php

/**
 * Search
 * 
 * Search business model.
 * 
 * @author Aldiansyah
 */

use Symfony\Component\Yaml\Yaml;

class Search_model extends CI_Model
{
    private $datatypes = [
        'post' => 'getPosts',
        'video' => 'getVideos',
        'product' => 'getProducts',
    ];

    public function getAll($type, $q)
    {
        return $this->{$this->datatypes[$type]}($q);
    }

    public function getPosts($q)
    {
        if(empty($q)) return [];
        $this->db->from('mein_posts');
        $this->db->like('title', $q);
        return $this->db->get()->result_array();
    }
    
    public function getVideos($q)
    {
        if(empty($q)) return [];
        $this->db->from('videos');
        $this->db->like('title', $q);
        return $this->db->get()->result_array();
    }

    public function getProducts($q)
    {
        if(empty($q)) return [];
        $this->db->from('products');
        $this->db->like('product_name', $q);
        return $this->db->get()->result_array();
    }
}
