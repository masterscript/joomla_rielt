<?php

defined('_JEXEC') or die;


class FlatsHelper extends JHelperContent
{
	public static $extension = 'com_flats';

	
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('Список недвижимости'),
			'index.php?option=com_flats&view=flats',
			$vName == 'flats'
		);

		/*JHtmlSidebar::addEntry(
			JText::_('Ответы'),
			'index.php?option=com_flats&view=answers',
			$vName == 'answers'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('Новостройки'),
			'index.php?option=com_categories&extension=com_flats',
			$vName == 'categories'
		);
	}

}
