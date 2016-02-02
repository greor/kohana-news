<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	'news' => array(
		'uri_callback' => '(/<category_uri>(/<element_uri>-<element_id>.html))(?<query>)',
		'regex' => array(
			'element_uri' => '[^/.,;?\n]+',
			'element_id' => '[0-9]+',
		),
		'defaults' => array(
			'directory' => 'modules',
			'controller' => 'news',
			'action' => 'index',
		)
	),
);

