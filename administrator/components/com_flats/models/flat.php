<?php
defined('_JEXEC') or die;

JLoader::register('FlatsHelper', JPATH_ADMINISTRATOR . '/components/com_flats/helpers/flats.php');

class FlatsModelFlat extends JModelAdmin
{

	public $typeAlias = 'com_flats.flat';


	protected $text_prefix = 'COM_FLATS';


	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$i = 0;

		if (!parent::checkCategoryId($categoryId))
		{
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $this->table->alias, $this->table->title);
			$this->table->title = $data['0'];
			$this->table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// New category ID
			$this->table->catid = $categoryId;

			
			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());
				return false;
			}

			parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$i] = $newId;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}
			$user = JFactory::getUser();

			if (!empty($record->catid))
			{
				return $user->authorise('core.delete', 'com_flat.category.' . (int) $record->catid);
			}
			else
			{
				return parent::canDelete($record);
			}
		}
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_flats.category.' . (int) $record->catid);
		}
		else
		{
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'Flat', $prefix = 'FlatsTable', $config = array())
	{
		return JTable::getInstance('Flat', 'FlatsTable', $config);
		//return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_flats.flat', 'flat', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('flat.id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			
			$form->setFieldAttribute('published', 'disabled', 'true');
			
			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			
			$form->setFieldAttribute('published', 'filter', 'unset');
			
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_flats.edit.flat.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			
			// Prime some default values.
			if ($this->getState('flat.id') == 0)
			{
				$app = JFactory::getApplication();
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_flats.flats.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData('com_flats.flat', $data);

		return $data;
	}
	
	
	
	public function save($data){
		$app = JFactory::getApplication();

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy'){
			$data['published'] = 0;
		}
		
		
		if(isset($data['id'])){
			$id = $data['id'];
		}else{
			$id = $this->getState('flat.id');
		}
		$db = JFactory::getDbo();
		if (parent::save($data)){
// сохранение изображений
$query = $db->getQuery(true);
			$query->select('path');
			$query->from('#__flats_images');
			$query->where('object_id = '.$id);
			$db->setQuery($query);
			$all_images = $db->loadColumn();
			
			/* добавление изображений */
			if(count($data['images'])>0){
											
				$dir = JPATH_ROOT.'/images/flats/'.$id;
				
				if(!JFolder::exists($dir)) JFolder::create($dir, 0777);
								
				foreach($data['images'] as $key=>$value){
					$object = new stdClass();
					if (!in_array($value, $all_images)) {
						$new_file = $id."_".rand(111111111, 999999999).".jpg";
						//if(!
						//copy(JPATH_ROOT.$value, '/images/flats/'.$id.$new_file);//){ return false; }
						JFile::copy(JPATH_ROOT.'/'.$value, $dir.'/'.$new_file);
						
						$object->object_id		= $id;
						$object->path			= '/images/flats/'.$id.'/'.$new_file;
						$object->orders			= $data['images_order'][$key];
						$object->title			= $data['images_title'][$key];
						$object->description	= $data['images_description'][$key];
						
						$result = $db->insertObject('#__flats_images', $object);
										
						unlink(JPATH_ROOT.'/'.$value);
					}else{
						$object->object_id		= $id;
						$object->path			= $value;
						$object->orders			= $data['images_order'][$key];
						$object->title			= $data['images_title'][$key];
						$object->description	= $data['images_description'][$key];
												
						$result = $db->updateObject('#__flats_images', $object, 'path');
					}	
					
				}
			}
			/*удаление лишних изображений*/
			$query = $db->getQuery(true);
			$query->select('path');
			$query->from('#__flats_images');
			$query->where('object_id = '.$id);
			$db->setQuery($query);
			$all_images1 = $db->loadColumn();
			
			jimport('joomla.filesystem.folder');
			if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
			//$path = JPATH_ROOT.DS.'images'.DS.'flats'.DS.$id;
			$path = JPATH_ROOT.DS.'images'.DS.'flats';
			$files = JFolder::files($path);
			jimport('mavik.thumb.generator');
			$thumbGenerator = new MavikThumbGenerator(array(
				'thumbDir' => 'tmp/thumbnails/flats', // Директория для превьюшек
				'quality' => 100, // Качество jpg. От 1 до 100.
				'subDirs' => false,
			));
			foreach($files as $key=>$img){
				if (isset($img)&& !empty($img)){
					if (!in_array("/images/flats/".$img, $all_images1)) {
						unlink(JPATH_ROOT."/images/flats/".$img);
					}else{
						$foto_hw = getimagesize(JPATH_ROOT."/images/flats/".$img);
						$width = $foto_hw[0];
						$height = $foto_hw[1];
						$k = $width/$height;
						if($width>$height){
							$n_width = 1200;
							$n_height = 1200/$k;
						}else{
							$n_height = 800;
							$n_width = $n_height*$k;
						}	
						if($width>1200){
							try {
								$image = $thumbGenerator->getThumb(JPATH_ROOT."/images/flats/".$img, $n_width, $n_height);
								$new_img = str_replace("/administrator", "", $image->thumbnail->url);
								JFile::copy(JPATH_ROOT.$new_img, JPATH_ROOT."/tmp/".$img);
								unlink(JPATH_ROOT."/images/flats/".$img);
								JFile::copy(JPATH_ROOT."/tmp/".$img, JPATH_ROOT."/images/flats/".$img);
								unlink(JPATH_ROOT."/tmp/".$img);
								unlink(JPATH_ROOT.$new_img);
							} catch (Exception $exc) {}
						}
					}
				}
			}
			
			/*ресайс больших*/
// конец изображения					
			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_FLATS_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where($db->quoteName('context') . ' = ' . $db->quote('com_flats.item'))
					->where($db->quoteName('id') . ' IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);
					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_flats.item') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}


	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry;
			/*$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();*/
		
		}

		// Load associated flats items
		$app = JFactory::getApplication();
		

		

		

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		if (empty($table->id)){
			// Set the values
			$table->created = $date->toSql();

			
		}
		else
		{
			// Set the values
			$table->modified = $date->toSql();
			$table->modified_by = $user->get('id');
		}

		// Increment the content version number.
		//$table->version++;
	}

	public function publish(&$pks, $value = 1)
	{
		$result = parent::publish($pks, $value);

		// Clean extra cache for flats
		$this->cleanCache('feed_parser');

		return $result;
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;
		return $condition;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association flats items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('title', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_FLATS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_flat');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	
}
