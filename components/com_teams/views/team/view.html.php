<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Teams class for the Teams Component
 */
class TeamsViewTeam extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->team = $this->get('Team');
		$this->team_files = $this->get('TeamFiles');
		$this->activities_support = $this->get('ActivitiesSupport');
		$this->activities_team = $this->get('ActivitiesTeam');
		// Display the view
		parent::display($tpl);
	}
}
