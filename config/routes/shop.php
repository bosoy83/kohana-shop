<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'shop_element' => array(
		'uri_callback' => '/<element_uri>.html(?<query>)',
		'defaults' => array(
			'directory' => 'modules',
			'controller' => 'shop',
			'action' => 'detail',
		)
	),
	'shop_category' => array(
		'uri_callback' => array('Helper_Shop', 'route'), 
		'regex' => '(/<category_uri>(/<element_uri>.html))(?<query>)',
		'defaults' => array(
			'directory' => 'modules',
			'controller' => 'shop',
			'action' => 'index',
		)
	),
);

