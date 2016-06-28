<?php
defined('_JEXEC') or die;
class FlatsViewCategory extends JViewCategory
{
	protected $defaultPageTitle = 'COM_FLATS_DEFAULT_PAGE_TITLE';
	protected $extension = 'com_flats';
	protected $viewName = 'flat';
	protected $languages = array();
	protected $consult = array();
	public function display($tpl = null)
	{
		parent::commonCategoryDisplay();
		
		
		return parent::display($tpl);
	}

	protected function prepareDocument()
	{
		//title
			$title = $this->params->get('menu_meta_title', '');
			
			if (empty($title)){
				$title = $this->params->get('page_title', '');			
			}
			if (empty($title)){
				$title = $this->category->title;
			}
			
			$this->document->setTitle($title);
			
		parent::prepareDocument();
		$id = (int) @$menu->query['id'];

		$menu = $this->menu;

		if ($menu && ($menu->query['option'] != 'com_flats' || $menu->query['view'] == 'flat' || $id != $this->category->id))
		{
			$path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while (($menu->query['option'] != 'com_flats' || $menu->query['view'] == 'flat' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => FlatsHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}

			$path = array_reverse($path);
			$bradcrumbs_short = $this->params->get('menu-meta_breadcrumbs_short', '');
			$bradcrumbs_full = $this->params->get('menu-meta_breadcrumbs_full', '');
			
			foreach ($path as $item)
			{	
				if(!empty($item['link']) && !empty($bradcrumbs_short)){
					$this->pathway->addItem($bradcrumbs_short, $item['link']);				
				}elseif(empty($item['link']) && !empty($bradcrumbs_full)){
					$this->pathway->addItem($bradcrumbs_full, $item['link']);
				}else{
					$this->pathway->addItem($item['title'], $item['link']);
				}
			}
		}

		parent::addFeed();
	}
}
