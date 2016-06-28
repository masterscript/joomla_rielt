<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldStaffs extends JFormFieldList
{

	protected $type = 'Staffs';

	protected function getOptions()
	{
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('id As value, last_name, first_name, second_name')
			->from('#__staffs AS a')
			->order('a.last_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
			
			for($i=0;$i<count($options);$i++){
				$options[$i]->text = $options[$i]->last_name." ".$options[$i]->first_name." ".$options[$i]->second_name;
			}
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
