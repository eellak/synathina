<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('StatsHelper', JPATH_ADMINISTRATOR . '/components/com_stats/helpers/stats.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 */
abstract class JHtmlStat
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $statid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public static function association($statid)
	{
	}

}
