<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_flats
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_flats
 *
 * @package     Joomla.Site
 * @subpackage  com_flats
 * @since       3.3
 */
class FlatsRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_flats component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$params = JComponentHelper::getParams('com_flats');
		$advanced = $params->get('sef_advanced_link', 0);

		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
		}

		$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
		$mId   = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

		if (isset($query['view']))
		{
			$view = $query['view'];

			if (empty($query['Itemid']) || empty($menuItem) || $menuItem->component != 'com_flats')
			{
				$segments[] = $query['view'];
			}

			unset($query['view']);
		}

		// Are we dealing with an article that is attached to a menu item?
		if (isset($query['view']) && ($mView == $query['view']) and (isset($query['id'])) and ($mId == (int) $query['id']))
		{
			unset($query['view']);
			unset($query['catid']);
			unset($query['id']);

			return $segments;
		}

		if (isset($view) and ($view == 'category' or $view == 'article'))
		{
			if ($mId != (int) $query['id'] || $mView != $view)
			{
				if ($view == 'article' && isset($query['catid']))
				{
					$catid = $query['catid'];
				}
				elseif (isset($query['id']))
				{
					$catid = $query['id'];
				}

				$menuCatid = $mId;
				$categories = JCategories::getInstance('Flats');
				$category = $categories->get($catid);

				if ($category)
				{
					$path = $category->getPath();
					$path = array_reverse($path);

					$array = array();

					foreach ($path as $id)
					{
						if ((int) $id == (int) $menuCatid)
						{
							break;
						}

						if ($advanced)
						{
							list($tmp, $id) = explode(':', $id, 2);
						}

						$array[] = $id;
					}

					$segments = array_merge($segments, array_reverse($array));
				}

				if ($view == 'article')
				{
					if ($advanced)
					{
						list($tmp, $id) = explode(':', $query['id'], 2);
					}
					else
					{
						$id = $query['id'];
					}

					$segments[] = $id;
				}
			}

			unset($query['id']);
			unset($query['catid']);
		}

		if (isset($query['layout']))
		{
			if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
			}
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$total = count($segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Get the active menu item.
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		$params = JComponentHelper::getParams('com_flats');
		$advanced = $params->get('sef_advanced_link', 0);

		// Count route segments
		$count = count($segments);

		// Standard routing for articles.
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['id']   = $segments[$count - 1];

			return $vars;
		}

		// From the categories view, we can only jump to a category.
		$id = (isset($item->query['id']) && $item->query['id'] > 1) ? $item->query['id'] : 'root';
		$categories = JCategories::getInstance('Flats')->get($id)->getChildren();
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = $advanced ? str_replace(':', '-', $segment) : $segment;

			foreach ($categories as $category)
			{
				if ($category->slug == $segment || $category->alias == $segment)
				{
					$vars['id'] = $category->id;
					$vars['catid'] = $category->id;
					$vars['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				if ($advanced)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true)
						->select($db->quoteName('id'))
						->from('#__flats')
						->where($db->quoteName('catid') . ' = ' . (int) $vars['catid'])
						->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
					$db->setQuery($query);
					$nid = $db->loadResult();
				}
				else
				{
					$nid = $segment;
				}

				$vars['id'] = $nid;
				$vars['view'] = 'article';
			}

			$found = 0;
		}

		return $vars;
	}
}

/**
 * Flats router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function FlatsBuildRoute(&$query)
{
	$router = new FlatsRouter;

	return $router->build($query);
}

function FlatsParseRoute($segments)
{
	$router = new FlatsRouter;

	return $router->parse($segments);
}