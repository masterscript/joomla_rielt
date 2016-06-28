<?php
defined('_JEXEC') or die;

class FlatsModelFlats extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'num', 'a.num',
				'num_pp', 'a.num_pp',
				'num_kv', 'a.num_kv',
				'dom', 'a.dom',
				'sekciya', 'a.sekciya',
				'etazh', 'a.etazh',
				'rooms', 'a.rooms',
				's_obch', 'a.s_obch',
				'sqm_price', 'a.sqm_price',
				'price', 'a.price',
				'planirovka', 'a.planirovka',
				'catid', 'a.catid',
				'published', 'a.published',
				'checked_out', 'a.checked_out',
				'price_2', 'a.price_2',
				'akcia', 'a.akcia',
			);

			$app = JFactory::getApplication();
			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$state = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $state);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', null);
		$this->setState('filter.category_id', $categoryId);
		
		$akcia = $this->getUserStateFromRequest($this->context . '.filter.akcia', 'filter_akcia', null);
		$this->setState('filter.akcia', $akcia);
		
		$sekciya = $this->getUserStateFromRequest($this->context . '.filter.sekciya', 'filter_sekciya', null);
		$this->setState('filter.sekciya', $sekciya);
		
		$rooms = $this->getUserStateFromRequest($this->context . '.filter.rooms', 'filter_rooms', null);
		$this->setState('filter.rooms', $rooms);
		
		$etazh = $this->getUserStateFromRequest($this->context . '.filter.etazh', 'filter_etazh', null);
		$this->setState('filter.etazh', $etazh);
		
		$forcedLanguage = $app->input->get('forcedLanguage');
		if (!empty($forcedLanguage)){
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}

		$params = JComponentHelper::getParams('com_flats');
		$this->setState('params', $params);

		parent::populateState('a.id', 'desc');
	}

	
	protected function getStoreId($id = ''){
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.akcia');
		$id .= ':' . $this->getState('filter.sekciya');
		$id .= ':' . $this->getState('filter.rooms');
		$id .= ':' . $this->getState('filter.etazh');
				
		return parent::getStoreId($id);
	}

	
	protected function getListQuery(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$query->select(
			$this->getState(
				'list.select',
				'a.*' 
			)
		);
		
		$query->from($db->quoteName('#__flats') . ' AS a');
		
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc){
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_houses.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}
		
		if ($access = $this->getState('filter.access')){
			$query->where('a.access = ' . (int) $access);
		}

		if (!$user->authorise('core.admin')){
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		$published = $this->getState('filter.published');
		if (is_numeric($published)){
			$query->where('a.published = ' . (int) $published);
		}elseif($published === ''){
			$query->where('(a.published IN (0, 1))');
		}
		
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)){
			$query->where('a.catid = ' . (int) $categoryId);
		}
		
		$akcia = $this->getState('filter.akcia');
		if (is_numeric($akcia)){
			$query->where('a.akcia = ' . (int) $akcia);
		}
		
		$sekciya = $this->getState('filter.sekciya');
		if (is_numeric($sekciya)){
			$query->where('a.sekciya = ' . (int) $sekciya);
		}
		
		$rooms = $this->getState('filter.rooms');
		if (is_numeric($rooms)){
			$query->where('a.rooms = ' . (int) $rooms);
		}
		
		$etazh = $this->getState('filter.etazh');
		if (is_numeric($etazh)){
			$query->where('a.etazh = ' . (int) $etazh);
		}
		
		$search = $this->getState('filter.search');
		if (!empty($search)){
			if (stripos($search, 'id:') === 0){
				$query->where('a.id = ' . (int) substr($search, 3));
			}else{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(
				a.num LIKE ' . $search . ' OR 
				a.sqm_price LIKE ' . $search . '
				)');
			}		
		}
		
		if ($language = $this->getState('filter.language')){
			$query->where('a.language = ' . $db->quote($language));
		}
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol == 'category_title'){
			$orderCol = 'c.title ' . $orderDirn;
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
