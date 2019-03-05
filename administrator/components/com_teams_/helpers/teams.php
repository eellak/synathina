<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * teams helper.
 *
 * @package     Core.Administrator
 * @subpackage  com_teams
 * @since       1.6
 */
class TeamsHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	$vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName = 'teams')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_TEAMS_SUBMENU_TEAMS'),
			'index.php?option=com_teams&view=teams',
			$vName == 'teams'
		);

		/*JHtmlSidebar::addEntry(
			JText::_('COM_TEAMS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_teams',
			$vName == 'categories'
		);*/
	}

}
