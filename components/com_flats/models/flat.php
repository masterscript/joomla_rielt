<?php
defined('_JEXEC') or die;
class FlatsModelFlat extends JModelItem{
	protected $_context = 'com_flats.flat';
	
	protected function populateState(){
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('flat.id', $pk);

		$offset = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_flats')) &&  (!$user->authorise('core.edit', 'com_flats'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}
	
	public function &getItem($pk = null)	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('flat.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select($this->getState('item.select', 'a.*'))
					->from('#__flats AS a');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
					->join('LEFT', '#__categories AS c on c.id = a.catid');

				// Join on user table.
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = a.created_by');
				
				$query->select('
					staff.last_name, staff.first_name, staff.second_name, 
					staff.job_title, staff.images as staff_images, 
					staff.alias as staff_alias, staff.id as staff_id, staff.catid as staff_catid, staff.phone as staff_phone, staff.mobile as staff_mobile
				')
					->join('LEFT', '#__staffs AS staff on staff.id = a.staff');
				
				$query->select('metro.name as metro_name')
					->join('LEFT', '#__wiki_type_metro as metro on metro.id = a.metro');
				
				$query->select('regions.name as regions_name')
					->join('LEFT', '#__wiki_type_regions as regions on regions.id = a.region');
				
				$query->select('object.name as object_name')
					->join('LEFT', '#__wiki_type_object as object on object.id = a.object_type');
				
				$query->select('walls.name as walls_name')
					->join('LEFT', '#__wiki_type_walls as walls on walls.id = a.type_walls');
				$query->select('highway.name as highway_name')
					->join('LEFT', '#__wiki_type_highway as highway on highway.id = a.highway');	
				
				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
					->join('LEFT', '#__categories as parent ON parent.id = c.parent_id')

					->where('a.id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->quote($db->getNullDate());
				$nowDate = $db->quote(JFactory::getDate()->toSql());

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');
				if (is_numeric($published))
				{
					$query->where('(a.published = ' . (int) $published . ' OR a.published =' . (int) $archived . ')')
						->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
						->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')')
						->where('(c.published = ' . (int) $published . ' OR c.published =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					JError::raiseError(404, JText::_('COM_FLATS_ERROR_FEED_NOT_FOUND'));
				}else{
					$query = $db->getQuery(true);
					$query->select($this->getState('item.select', '*'));
					$query->from('#__flat_images');
					$query->where('object_id = '.$data->id);
					$query->order('orders asc');
					$db->setQuery($query);
					$data->images = $db->loadObjectList();
					
					$query = $db->getQuery(true);
					$query->select($this->getState('item.select', 'id, alias'));
					$query->from('#__flats');
					$query->where('published = 1');
					$query->where('catid = '.$data->catid);
					$db->setQuery($query);
					$items = $db->loadObjectList();
					
					$first_object = $items[0]->id;
					$last_object = $items[count($items)-1]->id;
					
					$mass = array();
					$i = 0;
					foreach($items as $item){
						$mass[$i] = $item->id;
						if($item->id==$data->id){
							$i_m = $i; // определяем индекс нашего дома в массиве 
						}
						$i++;
					}
					if(isset($mass[$i_m+1])){ $next = $mass[$i_m+1]; }else{ $next = $first_object; }
					if(isset($mass[$i_m-1])){ $prew = $mass[$i_m-1]; }else{ $prew = $last_object; }
					
					$query = $db->getQuery(true);
					$query->select('alias, catid');
					$query->from('#__flats');
					$query->where('id = '.$next);
					$db->setQuery($query);
					$next_alias = $db->loadObject();
					$data->next_objects	= JRoute::_(FlatsHelperRoute::getFlatRoute($next.':'.$next_alias->alias, $next_alias->catid));
						
					$query = $db->getQuery(true);
					$query->select('alias, catid');
					$query->from('#__flats');
					$query->where('id = '.$prew);
					$db->setQuery($query);
					$prew_alias = $db->loadObject();
					$data->prew_objects	= JRoute::_(FlatsHelperRoute::getFlatRoute($prew.':'.$prew_alias->alias, $next_alias->catid));
								
					
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->published != $published) && ($data->published != $archived)))
				{
					JError::raiseError(404, JText::_('COM_FLATS_ERROR_FEED_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				//$registry->loadString($data->params);
				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$registry = new JRegistry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				// Compute access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();
					$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the flat.
	 *
	 * @param   int  $pk  Optional primary key of the item to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 *
	 * @since   3.0
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('flat.id');

			$table = JTable::getInstance('Flat', 'FlatsTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
}
