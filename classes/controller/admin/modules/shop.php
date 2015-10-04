<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Shop extends Controller_Admin_Front {

	protected $top_menu_item = 'modules';
	protected $sub_title = 'Shop';
	protected $category_id;
	protected $element_id;
	protected $module_config = 'shop';
	protected $_controller_name = array(
		'category' => 'shop_category',
		'element' => 'shop_element',
		'nomenclature' => 'shop_nomenclature',
	);
	
	public function before()
	{
		parent::before();
		$this->category_id = (int) Request::current()->query('category');
		$this->template
			->bind_global('CATALOG_CATEGORY_ID', $this->category_id);
		
		$this->element_id = (int) Request::current()->query('element');
		$this->template
			->bind_global('CATALOG_ELEMENT_ID', $this->element_id);
			
		$query_controller = $this->request->query('controller');
		if ( ! empty($query_controller) AND is_array($query_controller)) {
			$this->_controller_name = $this->request->query('controller');
		}
		$this->template
			->bind_global('CONTROLLER_NAME', $this->_controller_name);
	}

	protected function get_aside_view()
	{
		$menu_items = array_merge_recursive(
			$this->module_config->get('left_menu'),
			$this->_ex_menu_items
		);
		
		return parent::get_aside_view()
			->set('menu_items', $menu_items)
			->set('replace', array(
				'{CATEGORY_ID}' => $this->category_id,
				'{ELEMENT_ID}' => $this->element_id,
			));
	}

	protected function left_menu_category_list()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop' => array(
				'sub' => array(
					'list_category' => array(
						'title' => __('Categories list'),
						'link' => Route::url('modules', array(
							'controller' => $this->_controller_name['category'],
							'query' => 'category={CATEGORY_ID}'
						)),
					),
				),
			),
		));
	}
	
	protected function left_menu_category_add()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop' => array(
				'sub' => array(
					'add_category' => array(
						'title' => __('Add category'),
						'link' => Route::url('modules', array(
							'controller' => $this->_controller_name['category'],
							'action' => 'edit',
							'query' => 'category={CATEGORY_ID}'
						)),
					),
				),
			),
		));
	}
	
	protected function left_menu_category_fix()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'fix' => array(
				'title' => __('Fix positions'),
				'link'  => Route::url('modules', array(
					'controller' => $this->_controller_name['category'],
					'action' => 'position',
					'query' => 'mode=fix',
				)),
			),
		));
	}
	
	protected function left_menu_element_list()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop_elements' => array(
				'title' => __('Elements list'),
				'link' => Route::url('modules', array(
					'controller' => $this->_controller_name['element'],
					'query' => 'category={CATEGORY_ID}'
				)),
				'sub' => array(),
			),
		));
	}
	
	protected function left_menu_element_add()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop_elements' => array(
				'sub' => array(
					'add_element' => array(
						'title' => __('Add element'),
						'link' => Route::url('modules', array(
							'controller' => $this->_controller_name['element'],
							'action' => 'edit',
							'query' => 'category={CATEGORY_ID}'
						)),
					),
				),
			),
		));
	}
	
	protected function left_menu_nomenclature_list($link = FALSE)
	{
		if (empty($link)) {
			$link = Route::url('modules', array(
				'controller' => $this->_controller_name['nomenclature'],
				'query' => 'category={CATEGORY_ID}&element={ELEMENT_ID}'
			));
		}
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop_nomenclature' => array(
				'title' => __('Nomenclatures list'),
				'link' => $link,
				'sub' => array(),
			),
		));
	}
	
	protected function left_menu_nomenclature_add()
	{
		$query = 'category={CATEGORY_ID}&element={ELEMENT_ID}';
		if ( ! empty($this->back_url)) {
			$query .= '&back_url='.urlencode($this->back_url);
		}
		
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'shop_nomenclature' => array(
				'sub' => array(
					'add_nomenclature' => array(
						'title' => __('Add nomenclature'),
						'link' => Route::url('modules', array(
							'controller' => $this->_controller_name['nomenclature'],
							'action' => 'edit',
							'query' => $query
						)),
					),
				),
			),
		));
	}
	
} 
