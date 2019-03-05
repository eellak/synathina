<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of teams.
 *
 * @since  1.6
 */
class TeamsViewTeams extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		TeamsHelper::addSubmenu('teams');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo	= JHelperContent::getActions('com_teams', 'team', $this->state->get('filter.team_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title('Ομαδες - όταν ενεργοποιηθεί μία ομάδα στέλνεται email στον χρήστη');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_teams', 'core.create'))) > 0)
		{
			JToolbarHelper::addNew('team.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('team.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('teams.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('teams.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('teams.archive');
			JToolbarHelper::checkin('teams.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'teams.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('teams.trash');
		}

		if ($user->authorise('core.admin', 'com_teams') || $user->authorise('core.options', 'com_teams'))
		{
			JToolbarHelper::preferences('com_teams');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_CONTACTS_CONTACTS');

		JHtmlSidebar::setAction('index.php?option=com_teams');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		/*JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_teams'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);*/

		/*JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);*/

		JHtmlSidebar::addFilter(
			'- επιλέξτε για ομάδα -',
			'filter_create_actions',
			JHtml::_('select.options', TeamsHelper::getCreateOptions(), 'value', 'text', $this->state->get('filter.create_actions'))
		);
		
		JHtmlSidebar::addFilter(
			'- επιλέξτε για υποστηρικτή -',
			'filter_support_actions',
			JHtml::_('select.options', TeamsHelper::getSupportersOptions(), 'value', 'text', $this->state->get('filter.support_actions'))
		);	

		JHtmlSidebar::addFilter(
			'- επιλέξτε τύπο -',
			'filter_team_or_org',
			JHtml::_('select.options', TeamsHelper::getTypeOptions(), 'value', 'text', $this->state->get('filter.team_or_org'))
		);		
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
