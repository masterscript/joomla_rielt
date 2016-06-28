<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTypedistrict extends JFormFieldList
{

	protected $type = 'Typedistrict';

	protected function getOptions()
	{
		$options = array();

		$db		= JFactory::getDbo();
		
		$query	= $db->getQuery(true)
			->select('id, name')
			->from('#__wiki_type_township')
			->order('name ASC');
		$db->setQuery($query);
		$townships = $db->loadObjectList();
		
		foreach($townships as $township){
			$options[] = JHTML::_('select.option','<OPTGROUP>',$township->name);
			$query	= $db->getQuery(true)
				->select('id As value, name as text')
				->from('#__wiki_type_district AS a')
				->where('a.township_id = '.$township->id)
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
