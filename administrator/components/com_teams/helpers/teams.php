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
class TeamsHelper extends JHelperContent
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
			'Ομάδες',
			'index.php?option=com_teams&view=teams',
			$vName == 'teams'
		);

	}

	public static function getTypeOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.name AS text')
			->from('#__team_types AS a')
            ->where('published != -2 ');

		// Get the options.
		$db->setQuery($query);
		$types = [['value'=>10, 'text'=>'Ομάδα πολιτών'], ['value'=>11, 'text'=>'Φορέας Οργανισμός'], ['value'=>12, 'text'=>'Επιχείρηση/Εταιρεία'], ['value'=>13, 'text'=>'Ιδιώτης/Δημότης']];
		$options1 = json_decode(json_encode($types), FALSE);
		//print_r($options1);
		try
		{
			//$options = $db->loadObjectList();
			$options=$options1;
			//print_r($options);
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		//array_unshift($options, JHtml::_('select.option', ''));

		return $options;
	}

	public static function getSupportersOptions()
	{
		$options = array();
		$types=array(0=>array('value'=>1, 'text'=>'Υποστηρικτής'));
		$options1 = json_decode(json_encode($types), FALSE);
		//print_r($options1);
		try
		{
			$options=$options1;
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	public static function getCreateOptions()
	{
		$options = array();
		$types=array(0=>array('value'=>1, 'text'=>'Ομάδα'));
		$options1 = json_decode(json_encode($types), FALSE);
		//print_r($options1);
		try
		{
			$options=$options1;
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}


}
