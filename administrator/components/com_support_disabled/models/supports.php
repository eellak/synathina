<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_support
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * SupportList Model
 *
 * @since  0.0.1
 */
class SupportModelSupports extends JModelList
{
	

	protected function populateState($ordering = null, $direction = null)

	{	
		
		$this->setState('list.limit', 100); 

	}
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
 
		// Create the base select statement.
		$query->select('*')
              ->from($db->quoteName('#__team_donation_types'))
			  ->limit(0,500);
 
		return $query;
	}
}