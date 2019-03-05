<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamstats
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TeamstatsHelper', JPATH_ADMINISTRATOR . '/components/com_teamstats/helpers/teamstats.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamstats
 */
abstract class JHtmlTeamstat
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $teamstatid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public teamstatic function association($teamstatid)
	{
	}

}
