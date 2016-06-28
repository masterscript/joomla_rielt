<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTypehighway extends JFormFieldList
{

	protected $type = 'Typehighway';

	protected function getOptions()
	{
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('id As value, name as text')
			->from('#__wiki_type_highway AS a')
			->order('a.name ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $db->getMessage());
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
