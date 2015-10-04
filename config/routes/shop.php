<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'shop' => array(
		'uri_callback' => array('Helper_Shop', 'route'), 
		'regex' => '(/<category_uri>(/<element_uri>))(?<query>)',
		'defaults' => array(
			'directory' => 'modules',
			'controller' => 'shop',
			'action' => 'index',
		)
	),
);

