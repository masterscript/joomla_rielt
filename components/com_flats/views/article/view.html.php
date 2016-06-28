<?php
defined('_JEXEC') or die;
class FlatsViewArticle extends JViewLegacy
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
		
		$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$item->parent_slug = $item->category_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		// check if cache directory is writeable
		$cacheDir = JPATH_CACHE . '/';

		if (!is_writable($cacheDir))
		{
			JError::raiseNotice('0', JText::_('COM_SHOP_CACHE_DIRECTORY_UNWRITABLE'));
			return;
		}

		
		$params = $state->get('params');
		
		$active = $app->getMenu()->getActive();
		$temp = clone ($params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			
			if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id='.(string) $item->id)))
			{
				
				$article_params->merge($temp);
				$item->params = $article_params;

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				
				
				$item->params = $temp;

				
				if ($layout = $item->params->get('article_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that article params take priority
			$temp->merge($article_params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-article menu item)
			if ($layout = $item->params->get('article_layout'))
			{
				$this->setLayout($layout);
			}
		}

		
		// Get the current menu item
		$params = $app->getParams();

		// Get the article
		$article = $item;

		$temp = new JRegistry;
		$temp->loadString($item->params);
		$params->merge($temp);

		

		$feed_display_order = $params->get('feed_display_order', 'des');
		if ($feed_display_order == 'asc')
		{
			$article->items = array_reverse($article->items);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('params', $params);
		$this->assignRef('article', $article);
		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);
		if (!empty($msg))
		{
			$this->assignRef('msg', $msg);
		}
		$this->print = $print;

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_flats.article', $item->id);

		// Increment the hit counter of the article.
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
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SHOP_DEFAULT_PAGE_TITLE'));
		}
		if($this->item->price_2>0){
			$price = $this->item->price_2;
		}else{
			$price = $this->item->price;
		}
		if($this->item->rooms=='1'){
			$rooms = 'однокомнатн';
		}elseif($this->item->rooms=='2'){
			$rooms = 'двухкомнатн';
		}elseif($this->item->rooms=='3'){
			$rooms = 'трехкомнатн';
		}elseif($this->item->rooms=='4'){
			$rooms = 'четырехкомнатн';
		}
		
		if($this->item->catid=='10'){
			$title = 'Купить '.$rooms.'ую квартиру в Микрорайон 6а, ул. Некрасова, Реутов. '.number_format($price, 0, " ", " ").' рублей';
			$desc = 'Продажа '.$rooms.'ой квартиры в Микрорайон 6а, ул. Некрасова, Реутов. '.number_format($price, 0, " ", " ").' рублей, площадь '.$this->item->s_obch.' м². +7 (495) 504 15 95. ФЗ 214, ДДУ, ипотека. Официальный центр продажи квартир в новостройках в Реутове. ';			
		}elseif($this->item->catid=='14'){
			$title = 'Купить '.$rooms.'ую квартиру в ЖК «Маяк», ул. Некрасова, Реутов. '.number_format($price, 0, " ", " ").' рублей';
			$desc = 'Продажа '.$rooms.'ой квартиры в ЖК «Маяк», ул. Комсомольская, Реутов. '.number_format($price, 0, " ", " ").' рублей, площадь '.$this->item->s_obch.' м². +7 (495) 504 15 95. ФЗ 214, ДДУ, ипотека. Официальный центр продажи квартир в новостройках в Реутове. ';	
		}else{
			$title = '';
			$desc = '';
		}	
		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_flats' || $menu->query['view'] != 'article' || $id != $this->item->id))
		{
			
			

			$path = array(array('title' => $title, 'link' => ''));
			$category = JCategories::getInstance('Flats')->get($this->item->catid);
			while (($menu->query['option'] != 'com_flats' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => FlatsHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $item){				
				$pathway->addItem($item['title'], $item['link']);			
			}
		}

		$this->document->setTitle($title);
		$this->document->setDescription($desc);
		//$this->document->setMetadata('keywords', 'key');
		$this->document->setMetadata('robots', 'index, follow');
		//$this->document->setMetaData('title', 'title');
		$this->document->setMetaData('author', $this->item->category_title);

	}
}
