<?php
defined('_JEXEC') or die;
class StaffsViewStaff extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $print;
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// Get view related request variables.
		$print = $app->input->getBool('print');

		// Get model data.
		$state = $this->get('State');
		$item  = $this->get('Item');

		if ($item)
		{
			// Get Category Model data
			$categoryModel = JModelLegacy::getInstance('Category', 'StaffsModel', array('ignore_request' => true));
			$categoryModel->setState('category.id', $item->catid);
			$categoryModel->setState('list.ordering', 'a.from_name');
			$categoryModel->setState('list.direction', 'asc');

			// @TODO: $items is not used. Remove this line?
			$items = $categoryModel->getItems();
		}

		// Check for errors.
		// @TODO: Maybe this could go into JComponentHelper::raiseErrors($this->get('Errors'))
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Add router helpers.
		$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$item->parent_slug = $item->category_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		// check if cache directory is writeable
		$cacheDir = JPATH_CACHE . '/';

		if (!is_writable($cacheDir))
		{
			JError::raiseNotice('0', JText::_('COM_STAFFS_CACHE_DIRECTORY_UNWRITABLE'));
			return;
		}

		// Merge staff params. If this is single-staff view, menu params override staff params
		// Otherwise, staff params override menu item params
		$params = $state->get('params');
		$staff_params = clone $item->params;
		$active = $app->getMenu()->getActive();
		$temp = clone ($params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an staff view for this feed, then the menu item params take priority
			if (strpos($currentLink, 'view=staff') && (strpos($currentLink, '&id='.(string) $item->id)))
			{
				// $item->params are the staff params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$staff_params->merge($temp);
				$item->params = $staff_params;

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single staff, so the staff params take priority here
				// Merge the menu item params with the staff params so that the staff params take priority
				$temp->merge($staff_params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-staff menu item)
				if ($layout = $item->params->get('staff_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that staff params take priority
			$temp->merge($staff_params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-staff menu item)
			if ($layout = $item->params->get('staff_layout'))
			{
				$this->setLayout($layout);
			}
		}

		// Check the access to the staff
		$levels = $user->getAuthorisedViewLevels();

		if (!in_array($item->access, $levels) or ((in_array($item->access, $levels) and (!in_array($item->category_access, $levels)))))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Get the current menu item
		$params = $app->getParams();

		// Get the staff
		$staff = $item;

		$temp = new JRegistry;
		$temp->loadString($item->params);
		$params->merge($temp);

		

		$feed_display_order = $params->get('feed_display_order', 'des');
		if ($feed_display_order == 'asc')
		{
			$staff->items = array_reverse($staff->items);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('params', $params);
		$this->assignRef('staff', $staff);
		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);
		if (!empty($msg))
		{
			$this->assignRef('msg', $msg);
		}
		$this->print = $print;

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_staffs.staff', $item->id);

		// Increment the hit counter of the staff.
		$model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$mymeta = $this->item->metadata->toArray();
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_STAFFS_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this staff
		if ($menu && ($menu->query['option'] != 'com_staffs' || $menu->query['view'] != 'staff' || $id != $this->item->id))
		{
			// If this is not a single staff menu item, set the page title to the staff title
			if ($this->item->last_name)
			{
				$title = $this->item->last_name." ".$this->item->first_name." ".$this->item->second_name;
			}

			$path = array(array('title' => $this->item->last_name." ".$this->item->first_name." ".$this->item->second_name, 'link' => ''));
			$category = JCategories::getInstance('Staffs')->get($this->item->catid);
			while (($menu->query['option'] != 'com_staffs' || $menu->query['view'] == 'staff' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => StaffsHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $item)
			{				
				if(!empty($mymeta['breadcrumbs'])){
					$pathway->addItem($mymeta['breadcrumbs'], $item['link']);
				}else{
					$pathway->addItem($item['title'], $item['link']);
				}
				
			}
		}

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		if (empty($title))
		{
			$title = $this->item->from_name;
		}
		
		
		//$mymetatitle = $this->params->get('mymetatitle');	
		if(!empty($mymeta['mymetatitle'])){
			$this->document->setTitle($mymeta['mymetatitle']);
		}else{
			$this->document->setTitle($title);
		}

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaTitle') == '1')
		{
			if(!empty($mymeta['mymetatitle'])){
				$this->document->setMetaData('title', $mymeta['mymetatitle']);
			}else{
				$this->document->setMetaData('title', $this->item->last_name." ".$this->item->first_name." ".$this->item->second_name);
			}
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

		$mdata = $this->item->metadata->toArray();	
	
		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}
	}
}
