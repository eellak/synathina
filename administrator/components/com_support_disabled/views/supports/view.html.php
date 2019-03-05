<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_support
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * UserPhotos View
 *
 * @since  0.0.1
 */
class SupportViewSupports extends JViewLegacy
{
	/**
	 * Display the User Photos view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		//$this->filterForm    = $this->get('FilterForm');
		//$this->activeFilters = $this->get('ActiveFilters');
 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
		
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_SUPPORT'));
		JToolBarHelper::addNew('support.add');
		JToolBarHelper::editList('support.edit');
		JToolbarHelper::publish('supports.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('supports.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('', 'supports.delete');
	}
}