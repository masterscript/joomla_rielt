<?php

defined('_JEXEC') or die;


class StaffsHelper extends JHelperContent
{
	public static $extension = 'com_staffs';

	
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('Сотрудники'),
			'index.php?option=com_staffs&view=staffs',
			$vName == 'staffs'
		);

		JHtmlSidebar::addEntry(
			JText::_('Категории'),
			'index.php?option=com_categories&extension=com_staffs',
			$vName == 'categories'
		);
	}

}
