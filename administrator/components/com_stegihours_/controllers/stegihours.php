<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stegihours
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_stegihours
 * @since       1.6
 */
class StegihoursControllerStegihours extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  ContactControllerContacts
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}


	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Stegihours', $prefix = 'StegihoursModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
	
	
	public function unpublish()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("UPDATE #__stegi SET published='0' WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
			}
		}
		$mainframe->enqueueMessage('Item(s) unpublished successfully');	
		$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
	}
	
	public function publish($pks, $value = 1)
	{
		if ($_REQUEST['task']=='trash'){
			StegihoursControllerStegihours::trash();
		}
		elseif($_REQUEST['task']=='delete'){
			StegihoursControllerStegihours::delete();

		}
		else{
			$mainframe = JFactory::getApplication();
			$db = JFactory::getDBO();
			$publishvalue=1;
			
			if ($_REQUEST["task"]=="unpublish"){
				$publishvalue=0;
			}
			
			foreach ($_REQUEST["cid"] as $cid){
				$db->setQuery("UPDATE #__stegi SET published='".$publishvalue."' WHERE id='".intval($cid)."'");
				if(!$db->query()){
					JError::raiseWarning(100, $db->getError());
					$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
				}
			}
			
			$mainframe->enqueueMessage('Item(s) published successfully and emails has been sent');		
			$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
		}
	}
	
	public function trash($pks, $value = 1)
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("UPDATE #__stegi SET published='-2' WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
			}
		}
		$mainframe->enqueueMessage('Item(s) trashed successfully');		
		$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
	}
	
	
	public function delete()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("DELETE FROM #__stegi WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
			}
		}
		$mainframe->enqueueMessage('Item(s) deleted successfully');		
		$mainframe->redirect("index.php?option=com_stegihours&view=stegihours");
	}

}
