<?php
defined('JPATH_BASE') or die;
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldActivities extends JFormFieldList {
 
	public $type = 'Activities';
 
	// getLabel() left out
 
	protected function getOptions() {
		//db connection
		$db = JFactory::getDBO();
		//get all activities
		$query = 'SELECT id as value,name as text FROM #__team_activities WHERE published=1';
		$db->setQuery( $query );
		$activities = $db->loadObjectList();
		return $activities;
	}
}
?>