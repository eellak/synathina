<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_supportersemails
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('SupportersemailsHelper', JPATH_ADMINISTRATOR . '/components/com_supportersemails/helpers/supportersemails.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_supportersemails
 */
abstract class JHtmlSupportersemails
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $supportersemailid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public static function association($supportersemailsid)
	{
	}

}
