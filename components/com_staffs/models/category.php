<?php
defined('_JEXEC') or die;
class StaffsModelCategory extends JModelList
{
	protected $_item = null;
	protected $_articles = null;
	protected $_siblings = null;
	protected $_children = null;
	protected $_parent = null;
	protected $_category = null;
	protected $_categories = null;
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'last_name', 'a.last_name',
				'first_name', 'a.first_name',
				'second_name', 'a.second_name',
				'experience', 'a.experience',
				'job_title', 'a.job_title',
				'phone', 'a.phone',
				'mobile', 'a.mobile',
				'skype', 'a.skype',
				'email', 'a.email',
				'staff_type', 'a.staff_type',
				'ordering', 'a.ordering',
			);
		}

		parent::__construct($config);
	}

	public function getItems()
	{
		$items = parent::getItems();

		/*foreach ($items as $item)
		{
			if (!isset($this->_params))
			{
				$params = new JRegistry;
				$item->params = $params;
				$params->loadString($item->params);
			}
		}*/
		return $items;
	}
	
	protected function getListQuery(){
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'))
			->from($db->quoteName('#__staffs') . ' AS a')
			->where('a.access IN (' . $groups . ')');

		// Filter by category.
		if ($categoryId = $this->getState('category.id')){
			$query->where('a.catid = ' . (int) $categoryId)
				->join('LEFT', '#__categories AS c ON c.id = a.catid')
				->where('c.access IN (' . $groups . ')');
		}

		
			$query->where('a.published = 1');
		
		$query->order('a.ordering ASC');
		return $query;
	}

	
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_staffs');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);
		$orderCol = 'id';
		$this->setState('list.ordering', $orderCol);
		$listOrder = 'ASC';
		$this->setState('list.direction', $listOrder);

		$id = $app->input->get('id', 0, 'int');
		$this->setState('category.id', $id);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_staffs')) && (!$user->authorise('core.edit', 'com_staffs')))
		{
			$this->setState('filter.published', 1);
		}

		$this->setState('params', $params);
	}

	public function getCategory()
	{
		if (!is_object($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;

			if ($active)
			{
				$params->loadString($active->params);
			}

			$options = array();
			$options['countItems'] = $params->get('show_cat_items', 1) || $params->get('show_empty_categories', 0);
			$categories = JCategories::getInstance('Staffs', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));
			if (is_object($this->_item))
			{
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	public function getParent()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_parent;
	}

	
	function &getLeftSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_leftsibling;
	}

	function &getRightSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_rightsibling;
	}

	
	function &getChildren()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_children;
	}

	
	public function hit($pk = 0)
	{
		$input    = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk    = (!empty($pk)) ? $pk : (int) $this->getState('category.id');
			$table = JTable::getInstance('Category', 'JTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
}
