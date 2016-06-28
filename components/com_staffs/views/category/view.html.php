<?php
defined('_JEXEC') or die;
class StaffsViewCategory extends JViewCategory
{
	protected $defaultPageTitle = 'COM_STAFFS_DEFAULT_PAGE_TITLE';
	protected $extension = 'com_staffs';
	protected $viewName = 'staff';
	protected $no_consult = array();
	protected $consult = array();
	public function display($tpl = null)
	{
		parent::commonCategoryDisplay();
		
		
		foreach ($this->items as $item)
		{
			if($item->staff_type=='2'){
				$this->no_consult[] = $item;
			}else{
				$this->consult[] = $item;
			}
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$temp		= new JRegistry;
			$temp->loadString($item->params);
			$item->params = clone($this->params);
			$item->params->merge($temp);
		}

		return parent::display($tpl);
	}

	protected function prepareDocument()
	{
		parent::prepareDocument();
		$id = (int) @$menu->query['id'];

		$menu = $this->menu;

		if ($menu && ($menu->query['option'] != 'com_staffs' || $menu->query['view'] == 'staff' || $id != $this->category->id))
		{
			$path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while (($menu->query['option'] != 'com_staffs' || $menu->query['view'] == 'staff' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => StaffsHelperRoute::getCategoryRoute($category->id));
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
