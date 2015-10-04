<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'shop' => array(
			'title' => __('Shop'),
			'link' => Route::url('modules', array(
				'controller' => 'shop_category',
			)),
			'sub' => array(),
		),
	),
	'a2' => array(
		'resources' => array(
			'shop_category_controller' => 'module_controller',
			'shop_element_controller' => 'module_controller',
			'shop_category' => 'module',
			'shop_element' => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_shop_category_access' => array(
					'role' => 'main',
					'resource' => 'shop_category_controller',
					'privilege' => 'access',
				),
				'shop_category_edit' => array(
					'role' => 'main',
					'resource' => 'shop_category',
					'privilege' => 'edit',
				),
				'shop_category_fix' => array(
					'role' => 'main',
					'resource' => 'shop_category',
					'privilege' => 'fix_positions',
				),
				
				'controller_shop_element_access' => array(
					'role' => 'main',
					'resource' => 'shop_element_controller',
					'privilege' => 'access',
				),
				'shop_element_edit' => array(
					'role' => 'main',
					'resource' => 'shop_element',
					'privilege' => 'edit',
				),
				'shop_element_fix' => array(
					'role' => 'main',
					'resource' => 'shop_element',
					'privilege' => 'fix_positions',
				),
			),
		)
	),
);