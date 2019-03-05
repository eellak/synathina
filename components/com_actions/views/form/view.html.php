<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Actions class for the Actions Component
 */
class ActionsViewForm extends JViewLegacy
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
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$this->team = $this->get('Team');
		$this->team_activities = $this->get('TeamActivities');
		$this->teams = $this->get('Teams');
		if ($isroot) {
			$this->teams_users = $this->get('TeamsUsers');
		}
		$this->services = $this->get('Services');
		// Display the view
		parent::display($tpl);
	}
}
