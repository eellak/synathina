<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldTeamEdit extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'TeamEdit';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();
		$db = JFactory::getDbo();
		$query = " SELECT id, name "
				." FROM #__team_activities "
				." ORDER BY id ASC ";
				
		$db->setQuery($query);
		$i=0;
		$options[$i]['value']="";
		$options[$i]['text']="Please Select";	
		$i++;
		
		$rows=$db->loadObjectList();
		
		
		foreach($rows as $row){
			$options[$i]['value']=$row->id;
			$options[$i]['text']=trim($row->name);
			$i++;
		}
		//print_r($options);
		//die;

		return $options;
	}
}
