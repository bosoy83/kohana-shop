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
			$this->left_menu_element_list($this->category_id);
			$this->left_menu_element_add();
		}
	}

	public function action_edit()
	{
		$id = (int) $this->request->current()->param('id');
		if ( (bool) $id) {
			$this->title = __('Edit element');
		} else {
			$this->title = __('Add element');
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
		$sub_request = Request::factory($sub_link)
			->post($this->request->current()->post())
			->execute();
		
		$this->template
			->set_filename('modules/shop/frame')
			->set('html', $sub_request->body());
		
		if ($this->is_initial) {
			$category_orm = ORM::factory('shop_Category', $this->category_id);
			$orm = ORM::factory('shop_Element', $id);
				
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $category_orm, 'edit')) {
				$this->left_menu_category_add();
			}
			$this->left_menu_element_list($this->category_id);
			if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
				$this->left_menu_element_add();
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
	
} 
