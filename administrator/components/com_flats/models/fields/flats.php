<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFlats extends JFormFieldList
{

	protected $type = 'Flats';

	protected function getOptions()
	{
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('id As value, fio, text as vopros')
			->from('#__flats AS a')
			->order('a.fio');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
			for($i=0;$i<count($options);$i++){
				$options[$i]->text = $options[$i]->fio." - ".$options[$i]->vopros;
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
