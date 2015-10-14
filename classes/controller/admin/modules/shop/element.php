<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Shop_Element extends Controller_Admin_Modules_Shop {

	public function action_index()
	{
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_element',
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$sub_request = Request::factory($sub_link)
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
		
		if ($this->is_initial) {
			$category_orm = ORM::factory('shop_Category')
				->and_where('id', '=', $this->category_id)
				->find();
			
			$this->title = $category_orm->title;
			$this->sub_title = __('Elements list');
			
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $category_orm, 'edit')) {
				$this->left_menu_category_add();
			}
			$this->left_menu_element_list();
			$this->left_menu_element_add();
		}
	}

	public function action_edit()
	{
		$id = (int) $this->request->current()->param('id');
		$orm = ORM::factory('shop_Element', $id);
		
		if ($orm->loaded()) {
			$this->title = __('Edit element');
		} else {
			$this->title = __('Add element');
			$id = NULL;
		}
		
		if ($this->is_initial) {
			$category_orm = ORM::factory('shop_Category', $this->category_id);
				
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $category_orm, 'edit')) {
				$this->left_menu_category_add();
			}
			$this->left_menu_element_list();
			if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
				$this->left_menu_element_add();
			}
		}
		
		$sub_query_array = array(
			'controller' => array(
				'category' => 'shop_category',
				'element' => 'shop_element',
			)
		) + $this->request->current()->query();
		$sub_link = Route::url('modules', array(
			'controller' => 'catalog_element',
			'action' => 'edit',
			'id' => $id,
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
		$post = $this->request->current()
			->post();
		if (isset($post['properties'])) {
			unset($post['properties']);
		}
		
		$html = Request::factory($sub_link)
			->post($post)
			->execute()
			->body();
		
		$html = $this->_set_properties_tab($html, $orm);
		if ($orm->loaded()) {
			$html = $this->_set_nomenclature_tab($html, $orm);
		}
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $html);
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
			'controller' => 'catalog_element',
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
	
	private function _set_nomenclature_tab($html, $orm)
	{
		$this->left_menu_nomenclature_list('#tab-nomenclature');
		if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
			$this->left_menu_nomenclature_add();
		}
		
		$request = $this->request->current();
		$back_url = $request->url();
		$query = $request->query();
		if ( ! empty($query)) {
			$back_url .= '?'.http_build_query($query);
		}
		
		$sub_query_array = array(
			'category' => $query['category'],
			'element' => $orm->id,
			'back_url' => $back_url.'#tab-nomenclature',
		);
		
		$sub_link = Route::url('modules', array(
			'controller' => 'shop_nomenclature',
			'query' => Helper_Page::make_query_string($sub_query_array),
		));
			
		$html_nomenclature = Request::factory($sub_link)
			->execute()
			->body();
		
		$tab_nav_html = View_Admin::factory('modules/shop/layout/tab/nav', array(
			'code' => 'nomenclature',
			'title' => '<b>'.__('Nomenclatures list').'</b>',
		));
		
		$tab_pane_html = View_Admin::factory('modules/shop/layout/tab/pane', array(
			'code' => 'nomenclature',
			'content' => $html_nomenclature
		));
		
		$html = str_replace(array(
			'<!-- #tab-nav-insert# -->', '<!-- #tab-pane-insert# -->'
		), array(
			$tab_nav_html.'<!-- #tab-nav-insert# -->', $tab_pane_html.'<!-- #tab-pane-insert# -->'
		), $html);
		
		return $html;
	}
	
	private function _set_properties_tab($html, $orm)
	{
		$post = $this->request->current()
			->post('properties');
		
		if ($orm->loaded() AND ! empty($post)) {
			$helper_propery = new Helper_Property('shop.properties.element', $orm->object_name(), $orm->id);
			$helper_propery->set_user_id($this->user->id);
			
			$files = Arr::get($_FILES, 'properties', array());
			$properties = $post + Helper_Property::prepare_files($files);
			foreach ($properties as $_prop_name => $_value) {
				$helper_propery->set($_prop_name, $_value);
			}
		}
		
		if (empty($helper_propery)) {
			$helper_propery = new Helper_Property('shop.properties.element', $orm->object_name(), $orm->id);
		}
		
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
} 
