<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Opencalls class for the Opencalls Component
 */
class OpencallsViewEdit extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$model = $this->getModel();
		//$model->hit();		
		$this->opencalls = $this->get('Opencalls');
		$this->activities = $this->get('Activities');
		$this->images = $this->get('Images');
		$this->files = $this->get('Files');
		// Display the view
		parent::display($tpl);
	}
}
