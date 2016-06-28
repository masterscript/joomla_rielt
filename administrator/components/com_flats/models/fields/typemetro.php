<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTypemetro extends JFormFieldList
{

	protected $type = 'Typemetro';

	protected function getOptions()
	{
		$options = array();

		$db		= JFactory::getDbo();
		
		$query	= $db->getQuery(true)
			->select('distinct city')
			->from('#__wiki_type_metro')
			->order('city ASC');
		$db->setQuery($query);
		$citys = $db->loadObjectList();
		
		foreach($citys as $city){
			$options[] = JHTML::_('select.option','<OPTGROUP>',$city->city);
			$query	= $db->getQuery(true)
				->select('id As value, name as text')
				->from('#__wiki_type_metro AS a')
				->where('a.city = "'.$city->city.'"')
				->order('a.name ASC');
			$db->setQuery($query);
			$items = $db->loadObjectList();
			if ($items){
				foreach($items as $item) {
					$options[] = JHtml::_('select.option', $item->value, $item->text);
				}
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
