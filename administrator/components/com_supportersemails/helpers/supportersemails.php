<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_supportersemails
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Contact component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_supportersemails
 * @since       1.6
 */
class SupportersemailsHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			'Supportersemails',
			'index.php?option=com_supportersemails&view=supportersemails',
			$vName == 'supportersemails'
		);

	}

}
