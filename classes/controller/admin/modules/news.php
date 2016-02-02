<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_News extends Controller_Admin_Front {

	protected $module_config = 'news';
	protected $menu_active_item = 'modules';
	protected $title = 'News';
	protected $sub_title = 'News';
	
	protected $category_id;
	protected $controller_name = array(
		'category' => 'news_category',
		'element' => 'news_element',
	);
	
	public function before()
	{
		parent::before();
		$this->category_id = (int) Request::current()->query('category');
		$this->template
			->bind_global('CATEGORY_ID', $this->category_id);
	
		$query_controller = $this->request->query('controller');
		if ( ! empty($query_controller) AND is_array($query_controller)) {
			$this->controller_name = $this->request->query('controller');
		}
		$this->template
			->bind_global('CONTROLLER_NAME', $this->controller_name);
		
		$this->title = __($this->title);
		$this->sub_title = __($this->sub_title);
	}
	
	protected function layout_aside()
	{
		$menu_items = array_merge_recursive(
			Kohana::$config->load('admin/aside/news')->as_array(),
			$this->menu_left_ext
		);
		
		return parent::layout_aside()
			->set('menu_items', $menu_items)
			->set('replace', array(
				'{CATEGORY_ID}' =>	$this->category_id,
				'{PAGE_ID}' =>	$this->module_page_id,
			));
	}

	protected function left_menu_category_add($orm)
	{
		if ($this->acl->is_allowed($this->user, $orm, 'add') ) {
			$this->menu_left_add(array(
				'categories' => array(
					'sub' => array(
						'add' => array(
							'title' => __('Add category'),
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['category'],
								'action' => 'edit',
								'query' => 'page={PAGE_ID}'
							)),
						),
					),
				),
			));
		}
	}
	
	protected function left_menu_category_fix($orm)
	{
		$can_fix_all = $this->acl->is_allowed($this->user, $orm, 'fix_all');
		$can_fix_master = $this->acl->is_allowed($this->user, $orm, 'fix_master');
		$can_fix_slave = $this->acl->is_allowed($this->user, $orm, 'fix_slave');
		
		if ($can_fix_all OR $can_fix_master OR $can_fix_slave) {
			$this->menu_left_add(array(
				'categories' => array(
					'sub' => array(
						'fix' => array(
							'title' => __('Fix positions'),
							'link'  => Route::url('modules', array(
								'controller' => $this->controller_name['category'],
								'action' => 'position',
								'query' => 'page={PAGE_ID}&mode=fix'
							)),
						),
					),
				),
			));
		}
	}
	
	protected function left_menu_element_list()
	{
		$this->menu_left_add(array(
			'category_elements' => array(
				'title' => __('News list'),
				'link' => Route::url('modules', array(
					'controller' => $this->controller_name['element'],
					'query' => 'category={CATEGORY_ID}&page={PAGE_ID}'
				)),
				'sub' => array(),
			),
		));
	}
	
	protected function left_menu_element_add()
	{
		$this->menu_left_add(array(
			'category_elements' => array(
				'sub' => array(
					'add' => array(
						'title' => __('Add news'),
						'link' => Route::url('modules', array(
							'controller' => $this->controller_name['element'],
							'action' => 'edit',
							'query' => 'category={CATEGORY_ID}&page={PAGE_ID}'
						)),
					),
				),
			),
		));
	}
	
	protected function _get_breadcrumbs()
	{
		$query_array = array(
			'page' => $this->module_page_id,
		);
	
		return array(
			array(
				'title' => __('Categories'),
				'link' => Route::url('modules', array(
					'controller' => $this->controller_name['category'],
					'query' => Helper_Page::make_query_string($query_array),
				)),
			)
		);
	}
}

