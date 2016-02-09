<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'a2' => array(
		'resources' => array(
			'news_category_controller' => 'module_controller',
			'news_element_controller' => 'module_controller',
			'news_category' => 'module',
			'news' => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access_1' => array(
					'role' => 'base',
					'resource' => 'news_category_controller',
					'privilege' => 'access',
				),
				'controller_access_2' => array(
					'role' => 'base',
					'resource' => 'news_element_controller',
					'privilege' => 'access',
				),

				
				'news_category_add' => array(
					'role' => 'full',
					'resource' => 'news_category',
					'privilege' => 'add',
				),
				'news_category_edit_1' => array(
					'role' => 'full',
					'resource' => 'news_category',
					'privilege' => 'edit',
					'assertion' => array('Acl_Assert_Edit', array(
						'site_id' => SITE_ID,
					)),
				),
				'news_category_hide' => array(
					'role' => 'full',
					'resource' => 'news_category',
					'privilege' => 'hide',
					'assertion'	=> array('Acl_Assert_Hide', array(
						'site_id' => SITE_ID,
						'site_id_master' => SITE_ID_MASTER
					)),
				),
				'news_category_fix_all' => array(
					'role' => 'super',
					'resource' => 'news_category',
					'privilege' => 'fix_all',
				),
				'news_category_fix_master' => array(
					'role' => 'main',
					'resource' => 'news_category',
					'privilege' => 'fix_master',
				),
				'news_category_fix_slave' => array(
					'role' => 'full',
					'resource' => 'news_category',
					'privilege' => 'fix_slave',
				),

				
				'news_edit_1' => array(
					'role' => 'base',
					'resource' => 'news',
					'privilege' => 'edit',
					'assertion' => array('Acl_Assert_Edit', array(
						'site_id' => SITE_ID,
					)),
				),
				'news_hide' => array(
					'role' => 'base',
					'resource' => 'news',
					'privilege' => 'hide',
					'assertion' => array('Acl_Assert_Hide', array(
						'site_id' => SITE_ID,
						'site_id_master' => SITE_ID_MASTER
					)),
				),
			),
			'deny' => array()
		)
	),
);