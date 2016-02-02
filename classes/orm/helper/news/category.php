<?php defined('SYSPATH') or die('No direct script access.');

class ORM_Helper_News_Category extends ORM_Helper_Property_Support {

	protected $_property_config = 'news.properties.category';
	protected $_safe_delete_field = 'delete_bit';
	protected $_position_type = self::POSITION_COMPLEX;
	protected $_position_fields = array(
		'position' => array(
			'group_by' => array('site_id', 'page_id'),
		),
	);
	protected $_on_delete_cascade = array('news');

}
