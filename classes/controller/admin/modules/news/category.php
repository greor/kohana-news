<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_News_Category extends Controller_Admin_Modules_News {

	private $not_deleted_categories = array();

	public function action_index()
	{
		$orm = ORM::factory('news_Category')
			->where('page_id', '=', $this->module_page_id);
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count($paginator_orm->count_all());
		unset($paginator_orm);
		
		$list = $orm
			->paginator($paginator)
			->find_all();
		
		$this->template
			->set_filename('modules/news/category/list')
			->set('list', $list)
			->set('hided_list', $this->get_hided_list($orm->object_name()))
			->set('not_deleted_categories', $this->not_deleted_categories)
			->set('paginator', $paginator);
			
		$this->left_menu_category_add($orm);
		$this->left_menu_category_fix($orm);
		
		$this->title = __('Categories');
	}

	public function action_edit()
	{
		$request = $this->request->current();
		$this->category_id = $id = (int) $this->request->current()->param('id');
		$helper_orm = ORM_Helper::factory('news_Category');
		$orm = $helper_orm->orm();
		if ( (bool) $id) {
			$orm
				->and_where('id', '=', $id)
				->find();
			if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->title = __('Edit category');
		} else {
			$this->title = __('Add category');
		}
		
		if (empty($this->back_url)) {
			$query_array = array(
				'page' => $this->module_page_id,
			);
			$query_array = Paginator::query($request, $query_array);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['category'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
		
		if ($this->is_cancel) {
			$request->redirect($this->back_url);
		}

		$errors = array();
		$submit = Request::$current->post('submit');
		if ($submit) {
			try {
				if ($orm->loaded()) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
					$reload = FALSE;
				} else {
					$orm->page_id = $this->module_page_id;
					$orm->site_id = SITE_ID;
					$orm->creator_id = $this->user->id;
					$reload = TRUE;
				}

				$values = $this->meta_seo_reset(
					$this->request->current()->post(),
					'meta_tags'
				);
				
				if (empty($values['uri']) OR row_exist($orm, 'uri', $values['uri'])) {
					$values['uri'] = transliterate_unique($values['title'], $orm, 'uri');
				}
				
				$helper_orm->save($values + $_FILES);
				
				if ($reload) {
					if ($submit != 'save_and_exit') {
						$this->back_url = Route::url('modules', array(
							'controller' => $request->controller(),
							'action' => $request->action(),
							'id' => $orm->id,
							'query' => Helper_Page::make_query_string($request->query()),
						));
					}
						
					$request
						->redirect($this->back_url);
				}
			} catch (ORM_Validation_Exception $e) {
				$errors = $this->errors_extract($e);
			}
		}

		// If add action then $submit = NULL
		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			
			$properties = $helper_orm->property_list();
			
			$this->template
				->set_filename('modules/news/category/edit')
				->set('errors', $errors)
				->set('helper_orm', $helper_orm)
				->set('not_deleted_categories', $this->not_deleted_categories)
				->set('properties', $properties);
			
			$this->left_menu_category_add($orm);
			$this->left_menu_category_fix($orm);
		} else {
			$request
				->redirect($this->back_url);
		}
	}

	public function action_delete()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		
		$helper_orm = ORM_Helper::factory('news_Category');
		$orm = $helper_orm->orm();
		$orm
			->and_where('id', '=', $id)
			->find();
		
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
		if (in_array($orm->code, $this->not_deleted_categories)) {
			throw new HTTP_Exception_404();
		}
		
		if ($this->element_delete($helper_orm)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'page' => $this->module_page_id,
				);
				$query_array = Paginator::query($request, $query_array);
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['category'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
				
			$request
				->redirect($this->back_url);
		}
	}

	public function action_position()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$mode = $request->query('mode');
		$errors = array();
		$helper_orm = ORM_Helper::factory('news_Category');
		
		try {
			$this->element_position($helper_orm, $id, $mode);
		} catch (ORM_Validation_Exception $e) {
			$errors = $this->errors_extract($e);
		}

		if (empty($errors)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'page' => $this->module_page_id
				);
				if ($mode != 'fix') {
					$query_array = Paginator::query($request, $query_array);
				} 
				
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['category'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
		
			$request
				->redirect($this->back_url);
		}
	}

	public function action_visibility()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$mode = $request->query('mode');
		
		$orm = ORM::factory('news_Category')
			->where('page_id', '=', $this->module_page_id)
			->and_where('id', '=', $id)
			->find();
		
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'hide')) {
			throw new HTTP_Exception_404();
		}
		
		if (in_array($orm->code, $this->not_deleted_categories)) {
			throw new HTTP_Exception_404();
		}
		
		if ($mode == 'hide') {
			$this->element_hide($orm->object_name(), $orm->id);
		} elseif ($mode == 'show') {
			$this->element_show($orm->object_name(), $orm->id);
		}
		
		if (empty($this->back_url)) {
			$query_array = array(
				'page' => $this->module_page_id,
			);
			$query_array = Paginator::query($request, $query_array);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['category'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
		
		$request
			->redirect($this->back_url);
	}

	protected function _get_breadcrumbs()
	{
		$breadcrumbs = parent::_get_breadcrumbs();
	
		$query_array = array(
			'page' => $this->module_page_id,
		);
	
		$request = $this->request->current();
		if (in_array($request->action(), array('edit'))) {
			$id = (int) $this->request->current()->param('id');
			$category_orm = ORM::factory('news_Category')
				->where('id', '=', $id)
				->find();
			if ($category_orm->loaded()) {
				$breadcrumbs[] = array(
					'title' => $category_orm->title.' ['.__('edition').']',
				);
			} else {
				$breadcrumbs[] = array(
					'title' => ' ['.__('new category').']',
				);
			}
		}
	
		return $breadcrumbs;
	}

} 
