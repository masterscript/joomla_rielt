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
 * Content categories view.
 *
 * @package     Joomla.Site
 * @subpackage  com_staffs
 * @since       1.5
 */
class StaffsViewCategories extends JViewCategories
{
	/**
	 * Language key for default page heading
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $pageHeading = 'COM_STAFFS_DEFAULT_PAGE_TITLE';

	/**
	 * @var    string  The name of the extension for the category
	 * @since  3.2
	 */
	protected $extension = 'com_staffs';
}
