<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * teams team Controller
 *
 * @package     Core.Administrator
 * @subpackage  com_teams
 * @since       1.5
 */
class TeamsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			$cachable	If true, the view output will be cached
	 * @param   array  $urlparams	An array of safe url parameters and their variable teams, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/teams.php';

		$view   = $this->input->get('view', 'teams');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'team' && $layout == 'edit' && !$this->checkEditId('com_teams.edit.team', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_teams&view=teams', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
