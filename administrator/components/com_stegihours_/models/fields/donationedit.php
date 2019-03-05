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
class JFormFieldDonationEdit extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'DonationEdit';

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

		$i=0;
		$options[$i]['value']="";
		$options[$i]['text']="Please Select";	
		$i++;
		
		$query = " SELECT id,name "
				." FROM #__stegihour_donation_types 
					WHERE parent_id=0 "
				." ORDER BY id ASC ";		
		$db->setQuery($query);
		$rows=$db->loadObjectList();
		foreach($rows as $row){
			$query = " SELECT id, name "
					." FROM #__stegihour_donation_types WHERE parent_id=".$row->id." "
					." ORDER BY name ASC ";
					
			$db->setQuery($query);

			
			$rows1=$db->loadObjectList();
			
			$options[$i]['value']=$row->id;
			$options[$i]['text']=$row->name;	
			$i++;
			
			foreach($rows1 as $row1){
				$options[$i]['value']=$row1->id;
				$options[$i]['text']=str_replace("'", '`',str_replace('"', '', ' - '.trim($row1->name)));
				$i++;
			}			
		}

		//print_r($options);
		//die;

		return $options;
	}
}
