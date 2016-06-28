<?php
defined('_JEXEC') or die;

class FlatsCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__flats';
		$options['extension'] = 'com_flats';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}
