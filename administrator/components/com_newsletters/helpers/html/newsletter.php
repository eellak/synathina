<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsletters
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('NewslettersHelper', JPATH_ADMINISTRATOR . '/components/com_newsletters/helpers/newsletters.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsletters
 */
abstract class JHtmlNewsletter
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $newsletterid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public static function association($newsletterid)
	{
	}

}
