<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Shop_Nomenclature extends Controller_Admin_Modules_Shop {

	private $category;
	private $element;
	
	public function before()
	{
		parent::before();
		
		$this->category = ORM::factory('shop_Category')
			->where('id', '=', $this->category_id)
			->find();
		
		$this->element = ORM::factory('shop_Element')
			->where('id', '=', $this->element_id)
			->and_where('category_id', '=', $this->category_id)
			->find();
	}
	
	public function action_index()
	{
		$orm = ORM::factory('shop_Nomenclature');
		
		$this->title = __('Nomenclatures list');
		if ($this->element->loaded()) {
			$this->sub_title = $this->element->title;
			$paginator = FALSE;
			
			$list = $orm
				->where('product_id', '=', $this->element_id)
				->find_all();
		} else {
			$this->sub_title = FALSE;
			
			$paginator_orm = clone $orm;
			$paginator = new Paginator('admin/layout/paginator');
			$paginator
				->per_page(20)
				->count($paginator_orm->count_all());
			unset($paginator_orm);
			
			$list = $orm
				->paginator($paginator)
				->find_all();
		}
		
		$this->template
			->set_filename('modules/shop/nomenclature/list')
			->set('list', $list)
			->set('paginator', $paginator);
		
		if ($this->is_initial) {
			$this->left_menu_category_list();
			if ($this->acl->is_allowed($this->user, $this->category, 'edit')) {
				$this->left_menu_category_add();
			}
			$this->left_menu_element_list();
			$this->left_menu_element_add();
			$this->left_menu_nomenclature_list();
			if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
				$this->left_menu_nomenclature_add();
			}
		}
	}

	public function action_edit()
	{
		$id = (int) $this->request->current()->param('id');
		$helper_orm = ORM_Helper::factory('shop_Nomenclature');
		$orm = $helper_orm->orm();
		if ( (bool) $id) {
			$orm
				->where('id', '=', $id)
				->find();

			if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->title = __('Edit nomenclature');
		} else {
			$this->title = __('Add nomenclature');
		}
		$this->sub_title = __('Nomenclatures list');
		
		if (empty($this->back_url)) {
			$query_array = array(
				'category' => $this->category_id,
				'element' => $this->element_id,
			);
			$p = $this->request->current()->query( Paginator::QUERY_PARAM );
			if ( (bool) $id && ! empty($p)) {
				$query_array[ Paginator::QUERY_PARAM ] = $p;
			}
			$this->back_url = Route::url('modules', array(
				'controller' => $this->_controller_name['nomenclature'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}

		if ($this->is_cancel) {
			$this->request->current()
				->redirect($this->back_url);
		}
		
		if (empty($orm->sort)) {
			$orm->sort = 500;
		}
		
		$errors = array();
		$submit = $this->request->current()->post('submit');
		if ($submit) {
			try {
				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
				} else {
					$orm->creator_id = $this->user->id;
					$orm->product_id = $this->element_id;
				}

				$values = $orm->check_meta_fields(
					$this->request->current()->post(),
					'meta_tags'
				);
				
				$helper_orm->save($values + $_FILES);
				
				$helper_propery = new Helper_Property('shop.properties.nomenclature', $orm->object_name(), $orm->id);
				$helper_propery->set_user_id($this->user->id);
				if ( ! empty($values['properties'])) {
					$files = Arr::get($_FILES, 'properties', array());
					$properties = $values['properties'] + Helper_Property::prepare_files($files);
					foreach ($properties as $_prop_name => $_value) {
						$helper_propery->set($_prop_name, $_value);
					}
				}
				
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_files'])) {
					$errors = array_merge($errors, $errors['_files']);
					unset($errors['_files']);
				}
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			if ($this->is_initial) {
				$this->left_menu_category_list();
				if ($this->acl->is_allowed($this->user, $this->category, 'edit')) {
					$this->left_menu_category_add();
				}
				$this->left_menu_element_list();
				if ($this->acl->is_allowed($this->user, $this->element, 'edit')) {
					$this->left_menu_element_add();
				}
				$this->left_menu_nomenclature_list($this->back_url);
				if ($this->acl->is_allowed($this->user, $orm, 'edit')) {
					$this->left_menu_nomenclature_add();
				}
			}
			
			if (empty($helper_propery)) {
				$helper_propery = new Helper_Property('shop.properties.nomenclature', $orm->object_name(), $orm->id);
			}
			$properties = $helper_propery->get_list();
			
			$this->template
				->set_filename('modules/shop/nomenclature/edit')
				->set('errors', $errors)
				->set('helper_orm', $helper_orm)
				->set('properties', $properties);
		} else {
			$this->request->current()
				->redirect($this->back_url);
		}
	}
	
	public function action_delete()
	{
		$id = (int) $this->request->current()->param('id');
	
		$helper_orm = ORM_Helper::factory('shop_Nomenclature');
		$orm = $helper_orm->orm();
		$orm
			->and_where('id', '=', $id)
			->find();
	
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
	
		if ($this->delete_element($helper_orm)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'category' => $this->category_id,
					'element' => $this->element_id,
				);
				$this->back_url = Route::url('modules', array(
					'controller' => $this->_controller_name['nomenclature'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
			
			$this->request->current()
				->redirect($this->back_url);
		}
	}
	
	public function action_dyn_sort()
	{
		$this->auto_render = FALSE;
		
		$id = (int) $this->request->post('id');
		$field = $this->request->post('field');
		$value = $this->request->post('value');
		
		$orm = ORM::factory('shop_Nomenclature', $id);
		if (empty($field) OR ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
		try {
			$orm->values(array(
				$field => $value
			))->save();
		} catch (ORM_Validation_Exception $e) {
			throw new HTTP_Exception_404();
		}
		
		Ku_AJAX::send('json', $orm->$field);
	}
	
} 
