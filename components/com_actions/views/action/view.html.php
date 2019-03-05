<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Actions class for the Actions Component
 */
class ActionsViewAction extends JViewLegacy
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
		$this->action = $this->get('Action');
		$this->subactions = $this->get('Subactions');
		$this->similaractions = $this->get('SimilarActions');
		$this->team_activities = $this->get('TeamActivities');
		// Display the view
		parent::display($tpl);
	}
}
