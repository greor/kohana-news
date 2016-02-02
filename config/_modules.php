<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'news' => array(
		'alias' => 'greor-news',
		'name' => 'News module',
		'type' => Helper_Module::MODULE_MULTI,
		'controller' => 'news_category'
	),
);