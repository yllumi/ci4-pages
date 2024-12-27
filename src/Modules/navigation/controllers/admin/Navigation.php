<?php

use App\core\Backend_Controller;

/**
 * Cms
 *
 * Simple tool for making simple sites.
 *
 * @package		Pusaka
 * @author		Toni Haryanto (@toharyan)
 * @copyright	Copyright (c) 2011-2012, Nyankod
 * @license		http://nyankod.com/license
 * @link		http://nyankod.com/pusakacms
 */

class Navigation extends Backend_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->model(['navigation/Navigation_model','navigation/Nav_area_model']);
        $this->load->helper('navigation/navigation');
	}

	public function index()
	{
		$areas = $this->Nav_area_model
							->with_navigations('order_inside:nav_order asc')
							->get_all();

        // restructure
        if($areas){
            foreach($areas as $key => $area){
                if(!isset($area['navigations'])) continue;
                $areas[$key]['navigations'] = getStructuredNavigation($areas[$key]['navigations']);
            }
        }

		$this->view('admin/navigation', compact('areas'));
	}

	public function add_area()
	{
	 	$data['page_title'] = 'New Navigation Area';
		$data['form_type']  = 'new';
        $data['action_url'] = site_url('admin/navigation/insert_area');

		$this->view('admin/form_area', $data);
    }
    
    public function edit_area($id)
	{
		$data['page_title'] = 'Edit Navigation Area';
		$data['form_type']  = 'edit';
		$data['result']     = $this->Nav_area_model->get($id);
        $data['action_url'] = site_url('admin/navigation/update_area/'.$id);
        
		$this->view('admin/form_area', $data);
    }
    
    public function insert_area()
    {
        // Add this if you want to override post data
        // $this->Nav_area_model->set_form_data($post);

        $this->Nav_area_model->validate();
        $id = $this->Nav_area_model->insert();
        
        if (!$id){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
            redirect(getenv('HTTP_REFERER'));
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
        redirect('admin/navigation');
    }
    
    public function update_area($id)
    {
        // Add this if you want to override post data
        $post = $this->input->post(null, true);
        // $this->Nav_area_model->set_form_data($post);

        $this->Nav_area_model->where('id', $id);
        $this->Nav_area_model->validate('update');
        $update = $this->Nav_area_model->update();
        
        if (!$update){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
        redirect('admin/navigation');
    }

    public function delete_area($id, $slug)
    {
        $status = $this->Nav_area_model->delete($id);

        if ($status) {
            $this->session->set_flashdata('message', '<div class="alert alert-success">Area deleted Successfully.</div>');    
        }
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete.</div>');
        
        redirect('admin/navigation');
    }

    public function add_link($area = false)
	{
	 	$data['page_title'] = 'New Link';
		$data['form_type']  = 'new';
        $data['action_url'] = site_url('admin/navigation/insert_link');
        $data['areas'] = $this->Nav_area_model->as_dropdown('area_name')->get_all();
        $data['area'] = $area;
        $last_link = $this->Navigation_model->where('area_id', $area)->order_by('nav_order', 'desc')->get();
        $data['nav_order'] = ($last_link['nav_order'] ?? 0) +1;

        $parents = $this->Navigation_model->where('area_id', $area)->order_by('nav_order', 'asc')->getAll();
        $data['parents'] = ['' => '--root--'];
        $data['parents'] += getStructuredNavigationDropdown(getStructuredNavigation($parents));

		$this->view('admin/form_link', $data);
    }
    
    public function edit_link($id, $area = false)
	{
		$data['page_title'] = 'Edit Link';
		$data['form_type']  = 'edit';
		$data['result']     = $this->Navigation_model->get($id);
		$data['nav_order'] 	= $data['result']['nav_order'];
        $data['action_url'] = site_url('admin/navigation/update_link/'.$id);
        $data['areas'] = $this->Nav_area_model->as_dropdown('area_name')->get_all();
        $data['area'] = $area;

        $parents = $this->Navigation_model->where('area_id', $area)->order_by('nav_order', 'asc')->getAll();
        $data['parents'] = ['' => '--root--'];
        $data['parents'] += getStructuredNavigationDropdown(getStructuredNavigation($parents));
        unset($data['parents'][$id]);
        // dd($parents);
        
		$this->view('admin/form_link', $data);
    }
    
    public function insert_link()
    {
        // Add this if you want to override post data
        $post = $this->input->post(null, true);
        // $this->Navigation_model->set_form_data($post);

        $this->Navigation_model->validate();
        $id = $this->Navigation_model->insert();
        
        if (!$id){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');        
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully added.</div>');
        redirect('admin/navigation');
    }
    
    public function update_link($id)
    {
        // Add this if you want to override post data
        $post = $this->input->post(null, true);
        // $this->Navigation_model->set_form_data($post);

        $this->Navigation_model->where('id', $id);
        $this->Navigation_model->validate('update');
        $update = $this->Navigation_model->update();
        
        if (!$update){
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'. validation_errors() .'</div>');
            redirect(getenv('HTTP_REFERER'));
        } 

        $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated.</div>');
        redirect('admin/navigation');
    }

    public function delete_link($id, $area)
    {
        $status = $this->Navigation_model->delete($id);

        if ($status) {
            $this->session->set_flashdata('message', '<div class="alert alert-success">Link deleted Successfully.</div>');
        }
        else
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Fail to delete link.</div>');
        
        redirect('admin/navigation');
    }

    public function swap_link_order($id, $area, $direction = 'up')
	{
        $navigations = $this->Navigation_model->where('area_id', $area)->order_by('nav_order', 'asc')->getAll();
        
        $navigationsByID = array_combine(array_column($navigations, 'id'), $navigations);
        $navIDAndParentID = array_combine(array_column($navigations, 'id'), array_column($navigations, 'parent_id'));
        $currentNav = $navigationsByID[$id];
        $sameLevelNavs = array_keys(array_filter($navIDAndParentID, function ($val) use ($currentNav) {
            return $val == $currentNav['parent_id'];
        }));
        
        if($direction == 'up') {
            $prevID = $sameLevelNavs[array_search($id, $sameLevelNavs)-1]; 
            $target = $navigationsByID[$prevID];
        } else {
            $nextID = $sameLevelNavs[array_search($id, $sameLevelNavs)+1];
            $target = $navigationsByID[$nextID];
        }

        // Move current link position to temporary order first
		$this->Navigation_model
             ->where('area_id', $area)
             ->where('id', $id)
             ->update(['nav_order'=>-1]);

        // Move target link to current link
		$this->Navigation_model
             ->where('area_id', $area)
             ->where('id', $target['id'])
             ->update(['nav_order'=>$currentNav['nav_order']]);

        // Move current link to target position
		$this->Navigation_model
             ->where('area_id', $area)
             ->where('nav_order', -1)
             ->update(['nav_order'=>$target['nav_order']]);

		$this->session->set_flashdata('message', '<div class="alert alert-success">Link position swapped successfully.</div>');

		redirect('admin/navigation');
	}

	private function _generate_parent_dropdown($area_id, $current_link_id = false)
	{
		$parents = $this->Navigation_model
						->where('area_id', $area_id)
						->as_dropdown('caption')
						->get_all();
        $dropdown = [0 => '- no parent -'];
        if(!empty($parents)) $dropdown += $parents;
        if($current_link_id) unset($dropdown[$current_link_id]);

        return $dropdown;
	}
}
