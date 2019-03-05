<?php
defined('JPATH_BASE') or die;
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldTeam extends JFormFieldList {
 
	public $type = 'Team';
 
	// getLabel() left out
 
	protected function getOptions() {
		//db connection
		$db = JFactory::getDBO();
		//get all teams
		$query = 'SELECT id as value,title as text FROM #__teams WHERE state=1 ORDER BY ordering ASC ';
		$db->setQuery( $query );
		$teams = $db->loadObjectList();
		return $teams;
	}
}
?>