<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * HTML View class for the search component
 *
 * @since  1.0
 */
class ActionsViewActions extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Activities');
		$this->team_activities = $this->get('TeamActivities');
		$this->bestpractices = $this->get('BestPractices');
		$this->pagination = $this->get('Pagination');
		parent::display($tpl);
	}
}
