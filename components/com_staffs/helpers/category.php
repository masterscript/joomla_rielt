<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_staffs
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content Component Category Tree
 *
 * @package     Joomla.Site
 * @subpackage  com_staffs
 * @since       1.6
 */
class StaffsCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__staffs';
		$options['extension'] = 'com_staffs';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}
