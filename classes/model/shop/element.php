<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop_Element extends Model_Catalog_Element {
	
	protected function _initialize()
	{
		parent::_initialize();
		
		$this->_has_many['nomenclatures'] = array(
			'model' => 'shop_Nomenclature',
			'foreign_key' => 'product_id',
		);
	}
	
}
