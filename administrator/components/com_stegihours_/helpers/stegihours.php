<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_movies
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Contact component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_movies
 * @since       1.6
 */
class StegihoursHelper extends JHelperContent
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
			'Stegihours',
			'index.php?option=com_stegihours&view=stegihours',
			$vName == 'stegihours'
		);

	}

	public static function getAnnexeOptions()
	{
		$options = array();

		/*$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.name AS text')
			->from('#__annexes AS a')
            ->where('published != -2 ');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}*/

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		//array_unshift($options, JHtml::_('select.option', ''));

		return $options;
	}

	public static function getAreaOptions()
	{
		$options = array();
		$options = (object) array(0 => '1','2','3','4','5','6','7');
		return $options;
	}


	public static function getPainterOptions()
	{
		$options = array();

		/*$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, CONCAT(a.name, " ", a.firstname) AS text')
			->from('#__painters AS a')
            ->where('published != -2 ');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}*/

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		//array_unshift($options, JHtml::_('select.option', ''));

		return $options;
	}
}
