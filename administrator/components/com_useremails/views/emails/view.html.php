<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
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
class RequestsViewRequests extends JViewLegacy
{
	/**
	 * Display the User Photos view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null){
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
    // Set the toolbar
    $this->addToolbar();
		// Display the template
		parent::display($tpl);
	}
  protected function addToolbar()
	{
		// assuming you have other toolbar buttons ...
    JToolBarHelper::title( 'User Emails', 'generic.png' );
		//JToolBarHelper::custom('emails.export', 'extrahello.png', 'extrahello_f2.png', 'Export', true);
    //JToolBarHelper::preferences('com_useremails', '500');
	}
}
