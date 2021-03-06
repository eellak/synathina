<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_actions
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_actions
 * @since       1.6
 */
class ActionsControllerActions extends JControllerAdmin
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
	public function getModel($name = 'Actions', $prefix = 'ActionsModel', $config = array('ignore_request' => true))
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
			$db->setQuery("UPDATE #__actions SET published='0' WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_actions&view=actions");
			}
			$db->setQuery("UPDATE #__actions SET published='0' WHERE action_id='".intval($cid)."'");
			$db->query();
		}
		$mainframe->enqueueMessage('Item(s) unpublished successfully');
		$mainframe->redirect("index.php?option=com_actions&view=actions");
	}

	public function publish($pks, $value = 1)
	{
		if ($_REQUEST['task']=='trash'){
			ActionsControllerActions::trash();
		}
		elseif($_REQUEST['task']=='delete'){
			ActionsControllerActions::delete();

		}
		else{
			$mainframe = JFactory::getApplication();
			$db = JFactory::getDBO();
			$publishvalue=1;

			if ($_REQUEST["task"]=="unpublish"){
				$publishvalue=0;
			}

			foreach ($_REQUEST["cid"] as $cid){
				if($publishvalue==0){
					$config = JFactory::getConfig();
					require_once $config->get( 'abs_path' ).'/global_functions.php';
					$db	= JFactory::getDBO();
					$query = "SELECT u.email FROM #__users AS u INNER JOIN #__teams AS t ON t.user_id=u.id INNER JOIN #__actions AS a ON a.team_id=t.id WHERE a.id='".intval($cid)."' LIMIT 1";
					$db->setQuery($query);
					$email_to = $db->loadResult();
					$emails=array();
					if($email_to!=''){
						$emails=array($email_to);
					}
					$query = "SELECT name FROM #__actions WHERE id='".intval($cid)."' LIMIT 1 ";
					$db->setQuery($query);
					$action_name = $db->loadResult();
					//τεσσσσσσσσσσσσσσστ
					//$emails=array('ddasios@steficon.gr');
					$s_array=array($action_name);
					if ( synathina_email('action_cancelled_user',$s_array,$emails,'') ) {
							$mainframe->enqueueMessage(' Email have been sent');
					} else {
							$mainframe->enqueueMessage(' Problem sending email');
					}
					//print_r($_REQUEST);
					//die;
				}
				$db->setQuery("UPDATE #__actions SET published='".$publishvalue."' WHERE id='".intval($cid)."'");
				if(!$db->query()){
					JError::raiseWarning(100, $db->getError());
					$mainframe->redirect("index.php?option=com_actions&view=actions");
				}
				$db->setQuery("UPDATE #__actions SET published='".$publishvalue."' WHERE action_id='".intval($cid)."'");
				$db->setQuery("UPDATE #__stegihours SET published='".$publishvalue."' WHERE action_id='".intval($cid)."'");
				$db->query();
			}

			$mainframe->enqueueMessage('Item(s) published successfully and emails has been sent');
			$mainframe->redirect("index.php?option=com_actions&view=actions");
		}
	}

	public function trash($pks, $value = 1)
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("UPDATE #__actions SET published='-2' WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_actions&view=actions");
			}
		}
		$mainframe->enqueueMessage('Item(s) trashed successfully');
		$mainframe->redirect("index.php?option=com_actions&view=actions");
	}


	public function delete()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		foreach ($_REQUEST["cid"] as $cid){
			$db->setQuery("DELETE FROM #__actions WHERE id='".intval($cid)."'");
			if(!$db->query()){
				JError::raiseWarning(100, $db->getError());
				$mainframe->redirect("index.php?option=com_actions&view=actions");
			}
		}
		$mainframe->enqueueMessage('Item(s) deleted successfully');
		$mainframe->redirect("index.php?option=com_actions&view=actions");
	}

}
