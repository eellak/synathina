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
 * Support View
 *
 * @since  0.0.1
 */
class SupportViewSupport extends JViewLegacy
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
		$this->form = $this->get('Form');
		$this->item	= $this->get('Item');
 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
		
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		return parent::display($tpl);
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
		$input = JFactory::getApplication()->input;
		
		$isNew = ($this->item->id == 0);
 
		if ($isNew)
		{
			$title = JText::_('COM_SUPPORT_TYPE_NEW');
		}
		else
		{
			$title = JText::_('COM_SUPPORT_TYPE_EDIT');
		}
		
		JToolbarHelper::title($title, 'support');
		
		//JToolBarHelper::title(JText::_('COM_SUPPORT'));
		JToolbarHelper::apply('support.apply');
		JToolbarHelper::save('support.save');
		JToolbarHelper::save2new('support.save2new');
		JToolbarHelper::cancel('support.cancel',$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}