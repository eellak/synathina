<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamsexports
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('TeamsexportsHelper', JPATH_ADMINISTRATOR . '/components/com_teamsexports/helpers/teamsexports.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamsexports
 */
abstract class JHtmlTeamsexport
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $teamsexportid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public static function association($teamsexportid)
	{
	}

}
