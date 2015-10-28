<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Shop_Category extends Controller_Admin_Modules_Shop {

	public function action_index()
	{
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_category',
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$sub_request = Request::factory($sub_link)
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
		
		if ($this->is_initial) {
			$orm = ORM::factory('shop_Category', $this->category_id);
			$acl_edit = $this->acl->is_allowed($this->user, $orm, 'edit');
			
			$this->left_menu_category_list();
			if ($acl_edit) {
				$this->left_menu_category_add();
			}
			if ($this->category_id) {
				$this->left_menu_element_list();
				$this->title = $orm->title;
				$this->sub_title = __('Catalog');
			} else {
				$this->title = __('Catalog');
			}
			if ($this->acl->is_allowed($this->user, $orm, 'fix_positions')) {
				$this->left_menu_category_fix();
			}
		}
	}
	
	public function action_edit()
	{
		$id = (int) $this->request->current()->param('id');
		$orm = ORM::factory('shop_Category', $id);
		
		if ($orm->loaded()) {
			$this->title = __('Edit category');
		} else {
			$this->title = __('Add category');
		}
		
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_category',
			'action' => 'edit',
			'id' => $id,
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$post = $this->request->current()
			->post();
		if (isset($post['properties'])) {
			unset($post['properties']);
		}
		
		$this->_save_properties_tab($orm);
		
		$sub_request = Request::factory($sub_link)
			->post($post)
			->execute();
		
		$html = $sub_request->body();
		$html = $this->_set_properties_tab($html, $orm);
			
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $html);
		
		if ($this->is_initial) {
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
				$this->left_menu_category_add();
			}
			
			if ($orm->loaded()) {
				$this->left_menu_element_list();
			}
		}
	}
	
	
	public function action_delete()
	{
		$id = (int) $this->request->current()->param('id');
	
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_category',
			'action' => 'delete',
			'id' => $id,
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$sub_request = Request::factory($sub_link)
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
	}
	
	public function action_position()
	{
		$id = (int) $this->request->current()->param('id');
	
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_category',
			'action' => 'position',
			'id' => $id,
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$sub_request = Request::factory($sub_link)
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
	}
	
	private function _set_properties_tab($html, $orm)
	{
		$helper_propery = new Helper_Property('shop.properties.category', $orm->object_name(), $orm->id);
	
		$properties = $helper_propery->get_list();
		if ( ! empty($properties)) {
			$html_properties = View_Admin::factory('form/property/list', array(
				'properties' => $properties,
			));
		
			$tab_nav_html = View_Admin::factory('modules/shop/layout/tab/nav', array(
				'code' => 'properties',
				'title' => __('Properties'),
			));
			$tab_pane_html = View_Admin::factory('modules/shop/layout/tab/pane', array(
				'code' => 'properties',
				'content' => $html_properties
			));
		
			$html = str_replace(array(
				'<!-- #tab-nav-insert# -->', '<!-- #tab-pane-insert# -->'
			), array(
				$tab_nav_html.'<!-- #tab-nav-insert# -->', $tab_pane_html.'<!-- #tab-pane-insert# -->'
			), $html);
		}
	
		return $html;
	}
	
	private function _save_properties_tab($orm)
	{
		$post = $this->request->current()
			->post('properties');
		
		if ($orm->loaded() AND ! empty($post)) {
			$helper_propery = new Helper_Property('shop.properties.category', $orm->object_name(), $orm->id);
			$helper_propery->set_user_id($this->user->id);
		
			$files = Arr::get($_FILES, 'properties', array());
			$properties = $post + Helper_Property::extract_files($files);
			foreach ($properties as $_prop_name => $_value) {
				$helper_propery->set($_prop_name, $_value);
			}
		}
	}
} 
