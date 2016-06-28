<?php
defined('_JEXEC') or die;
class FlatsViewFlat extends JViewLegacy{
	protected $state;
	protected $item;
	protected $print;
	public function display($tpl = null){
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
			$categoryModel = JModelLegacy::getInstance('Category', 'FlatsModel', array('ignore_request' => true));
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
			JError::raiseNotice('0', JText::_('COM_FLATS_CACHE_DIRECTORY_UNWRITABLE'));
			return;
		}

		// Merge flat params. If this is single-flat view, menu params override flat params
		// Otherwise, flat params override menu item params
		$params = $state->get('params');
		$flat_params = clone $item->params;
		$active = $app->getMenu()->getActive();
		$temp = clone ($params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an flat view for this feed, then the menu item params take priority
			if (strpos($currentLink, 'view=flat') && (strpos($currentLink, '&id='.(string) $item->id)))
			{
				// $item->params are the flat params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$flat_params->merge($temp);
				$item->params = $flat_params;

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single flat, so the flat params take priority here
				// Merge the menu item params with the flat params so that the flat params take priority
				$temp->merge($flat_params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-flat menu item)
				if ($layout = $item->params->get('flat_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that flat params take priority
			$temp->merge($flat_params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-flat menu item)
			if ($layout = $item->params->get('flat_layout'))
			{
				$this->setLayout($layout);
			}
		}

		// Check the access to the flat
		$levels = $user->getAuthorisedViewLevels();

		if (!in_array($item->access, $levels) or ((in_array($item->access, $levels) and (!in_array($item->category_access, $levels)))))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Get the current menu item
		$params = $app->getParams();

		// Get the flat
		$flat = $item;

		$temp = new JRegistry;
		$temp->loadString($item->params);
		$params->merge($temp);

		
		
		

		$feed_display_order = $params->get('feed_display_order', 'des');
		if ($feed_display_order == 'asc')
		{
			$flat->items = array_reverse($flat->items);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('params', $params);
		$this->assignRef('flat', $flat);
		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);
		if (!empty($msg))
		{
			$this->assignRef('msg', $msg);
		}
		$this->print = $print;

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_flats.flat', $item->id);

		// Increment the hit counter of the flat.
		$model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();

		return parent::display($tpl);
	}

	protected function _prepareDocument()	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('Страница объекта'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this flat
		if ($menu && ($menu->query['option'] != 'com_flats' || $menu->query['view'] != 'flat' || $id != $this->item->id))
		{
			// If this is not a single flat menu item, set the page title to the flat title
			if ($this->item->title)
			{
				$title = $this->item->title;
			}

			$path = array(array('title' => $this->item->title, 'link' => ''));
			$category = JCategories::getInstance('Flats')->get($this->item->catid);
			while (($menu->query['option'] != 'com_flats' || $menu->query['view'] == 'flat' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => FlatsHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}
		$rooms = "";
		switch ($this->item->rooms) {
			case 1:
				$rooms = "однокомнатн";
				break;
			case 2:
				$rooms = "двухкомнатн";
				break;
			case 3:
				$rooms = "трехкомнатн";
				break;
			case 4:
				$rooms = "четырехкомнатн";
				break;	
			case 5:
				$rooms = "пятикомнатн";
				break;	
			case 6:
				$rooms = "шестикомнатн";
				break;	
		}
		$c_f = "";
		switch ($this->item->count_floor) {
			case 1:
				$c_f = "одноэтажный";
				break;
			case 2:
				$c_f = "двухэтажный";
				break;
			case 3:
				$c_f = "трехэтажный";
				break;
			case 4:
				$c_f = "четырехэтажный";
				break;	
			case 5:
				$c_f = "пятиэтажный";
				break;	
			case 6:
				$c_f = "шестиэтажный";
				break;	
		}
		
		
		
		if($this->item->status=='1'){ $this->item->status_name = 'отличное'; }
		elseif($this->item->status=='2'){ $this->item->status_name = 'хорошее'; }
		elseif($this->item->status=='3'){ $this->item->status_name = 'удовлетворительное';}else{
			$this->item->status_name = 'хорошее';
		}	
		
		if($this->item->street_type=='1'){ $this->item->street_type_name = 'улица'; }
		elseif($this->item->street_type=='2'){ $this->item->street_type_name = 'шоссе'; }
		elseif($this->item->street_type=='3'){ $this->item->street_type_name = 'проспект'; }
		elseif($this->item->street_type=='4'){ $this->item->street_type_name = 'проезд'; }
		elseif($this->item->street_type=='5'){ $this->item->street_type_name = 'площадь'; }
		elseif($this->item->street_type=='6'){ $this->item->street_type_name = 'переулок'; }
		elseif($this->item->street_type=='7'){ $this->item->street_type_name = 'бульвар'; }
		else{$this->item->street_type_name = "";}
		
		if (isset($this->item->time_to_metro_1) && !empty($this->item->time_to_metro_1)){
			$t_m = $this->item->time_to_metro_1;
		}elseif(isset($this->item->time_to_metro_2) && !empty($this->item->time_to_metro_2)){
			$t_m = $this->item->time_to_metro_2;
		}else{$t_m = "";}
		
		if($this->item->bargain_type=='1'){ $this->item->bargain_type_name = 'Купить'; $tttt = 'продажа'; }
elseif($this->item->bargain_type=='2'){ $this->item->bargain_type_name = 'Снять'; $tttt = 'аренда';  }
		else{$tttt ="";}
		if($this->item->catid=='15'){ //вторичная
			$title = 'Купить '.$rooms.'ую квартиру  '.number_format($this->item->price, 0, ',', ' ').' р. м. '.$this->item->metro_name;
			$desc = 'Звоните '.preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "+7 $1 $2 $3", $this->item->broker_phone).' продажа квартиры '.$this->item->general_space.' м, '.$this->item->status_name.' состояние, '.$t_m.' мин до м. '.$this->item->metro_name.', '.$this->item->street_type_name.' '.$this->item->street.', быстрая продажа, все документы, фото, ипотека';
		}elseif($this->item->catid=='16'){ //загородная
			$title = 'Купить '.$c_f.' загородный дом '.number_format($this->item->price, 0, ',', ' ').' '.$this->item->city;
			$desc = 'Звоните '.preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "+7 $1 $2 $3", $this->item->broker_phone).' продажа загородного дома '.$this->item->general_space.' м2, '.$this->item->status_name.' состояние, '.$this->item->city.','.$this->item->km_ot_mkad.' км до МКАД, участок '.$this->item->garder_space.' соток, '.$this->item->count_floor.' этажа, все документы, фото';
		}elseif($this->item->catid=='17'){ //аренда
			$title = 'Снять '.$rooms.'ую квартиру  '.number_format($this->item->price, 0, ',', ' ').' р. м. '.$this->item->metro_name;
			$desc = 'Звоните '.preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "+7 $1 $2 $3", $this->item->broker_phone).' аренда '.$rooms.'ой квартиры долгосрочно, '.$this->item->status_name.' состояние, '.$t_m.' мин до м. '.$this->item->metro_name.', '.$this->item->street_type_name.' '.$this->item->street.', быстрый въезд,  договор, фото, хорошие хозяева';
		}elseif($this->item->catid=='18'){ //коммерческая
			$title = $this->item->bargain_type_name.' '.$this->item->object_name.' с отдельным входом '.number_format($this->item->price, 0, ',', ' ').' р. м. '.$this->item->metro_name;
			$desc = 'Звоните '.preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "+7 $1 $2 $3", $this->item->broker_phone).' '.$tttt.' '.$this->item->object_name.'а '.$this->item->general_space.' м2, '.$this->item->status_name.' состояние, с ремонтом, '.$t_m.' мин до м. '.$this->item->metro_name.', '.$this->item->street_type_name.' '.$this->item->street.', быстрая '.$tttt.', все документы, фото';
		}
		
		$this->document->setTitle($title);
		$this->document->setDescription($desc);
		/*		
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
			$title = $this->item->title;
		}
		$this->document->setTitle($title);
		*/
		/*if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		*/
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
			$this->document->setMetaData('title', $this->item->title);
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
