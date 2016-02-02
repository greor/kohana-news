<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'categories' => array(
		'title' => __('Categories'),
		'link' => Route::url('modules', array(
			'controller' => 'news_category',
			'query' => 'page={PAGE_ID}'
		)),
		'sub' => array(),
	),
);