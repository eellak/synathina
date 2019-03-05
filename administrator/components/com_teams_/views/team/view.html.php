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
 * View to edit a weblink.
 *
 * @package     Core.Administrator
 * @subpackage  com_teams
 * @since       1.5
 */
class TeamsViewTeam extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		//$canDo		= JHelperContent::getActions($this->item->catid, 0, 'com_teams');

		JToolbarHelper::title(JText::_('COM_TEAMS_MANAGER_TEAM'), 'link teams');

		// If not checked out, can save the item.
		//if (!$checkedOut )
		//{
			JToolbarHelper::apply('team.apply');
			JToolbarHelper::save('team.save');
		//}
		//if (!$checkedOut && (count($user->getAuthorisedCategories('com_teams', 'core.create'))))
		//{
			JToolbarHelper::save2new('team.save2new');
		//}
		// If an existing item, can save to a copy.
		if (!$isNew)
		{
			JToolbarHelper::save2copy('team.save2copy');
		}
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('team.cancel');
		}
		else
		{
			if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_teams.team', $this->item->id);
			}

			JToolbarHelper::cancel('team.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_TEAMS_LINKS_EDIT');
	}
}
