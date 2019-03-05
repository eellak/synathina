<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_userphotos
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Photos Controller
 *
 * @since  0.0.1
 */
class SupportControllerSupports extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Support', $prefix = 'SupportModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
 
		return $model;
	}
	
	public function unpublish()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("UPDATE #__team_donation_types SET published='0' WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_support&view=supports");
			}
		}
		$mainframe->enqueueMessage('Item(s) unpublished successfully');	
		$mainframe->redirect("index.php?option=com_support&view=supports");
	}
	
	public function publish(&$pks, $value = 1)
	{		
			$mainframe = JFactory::getApplication();
			$db = JFactory::getDBO();
			$publishvalue=1;
			
			if ($_REQUEST["task"]=="unpublish"){
				$publishvalue=0;
			}
			
			foreach ($_REQUEST["cid"] as $cid){
				$db->setQuery("UPDATE #__team_donation_types SET published='".$publishvalue."' WHERE id='".intval($cid)."'");
				if(!$db->query()){
					JError::raiseWarning(100, $db->getError());
					$mainframe->redirect("index.php?option=com_support&view=supports");
				}
			}
			
			$mainframe->enqueueMessage('Item(s) published successfully');		
			$mainframe->redirect("index.php?option=com_support&view=supports");
	}
	
	public function delete()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
		
		foreach ($_REQUEST["cid"] as $cid){
			
			$db->setQuery("DELETE FROM #__team_donation_types WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_support&view=supports");
			}
		}
		
		$mainframe->enqueueMessage('Item(s) deleted successfully');		
		$mainframe->redirect("index.php?option=com_support&view=supports");
	}
}