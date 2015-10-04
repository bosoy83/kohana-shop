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
		if ( (bool) $id) {
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
		$sub_request = Request::factory($sub_link)
			->post($this->request->current()->post())
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
		
		if ($this->is_initial) {
			$orm = ORM::factory('shop_Category', $this->category_id);
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
				$this->left_menu_category_add();
			}
			
			if ( (bool) $id) {
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
	
} 
