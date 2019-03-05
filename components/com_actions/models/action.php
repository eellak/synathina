<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Actions Model
 */
class ActionsModelAction extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		// Get the message catid
		$catid = JRequest::getInt('catid');
		$this->setState('message.catid', $catid);
		// Get the pagination
		$page = JRequest::getInt('page');
		$this->setState('message.page', $page);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Actions', $prefix = 'Actions', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

    /**
     * Load an JSON string into the registry into the given namespace [or default if a namespace is not given]
     *
     * @param    string    JSON formatted string to load into the registry
     * @return    boolean True on success
     * @since    1.5
     * @deprecated 1.6 - Oct 25, 2010
     */
    public function loadJSON($data)
    {
        return $this->loadString($data, 'JSON');
    }

	public function getAction()
	{
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');

		$activityClass = new RemotedbActivity();

		$fields = ['*'];
		$where = "id='".@$_REQUEST['id']."' ".($isroot==1?'':'AND published=1')."";
		$action = $activityClass->getActivity($fields, $where, 1);

		if ($action) {
			return $action;
		} else {
			header('Location:'.JURI::root());
			exit();
		}
	}

	// public function getSimilar()
	// {
	// 	$activityClass = new RemotedbActivity();

	// 	$where = "action_id='".@$_REQUEST['id']."' AND published=1";

	// 	return $activityClass->getActivities([], $where);
	// }

	public function getSubactions()
	{
		$activityClass = new RemotedbActivity();
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$where = "action_id='".@$_REQUEST['id']."'".($isroot ? '' : ' AND published=1');
		$order = "action_date_start ASC";

		return $activityClass->getActivities([], $where, $order);
	}

	public function getTeamActivities()
	{
		$db = JFactory::getDBO();

		$query = "SELECT *
						FROM #__team_activities
						WHERE published=1 ";
		$db->setQuery($query);
		$activities = $db->loadObjectList();

		return $activities;
	}

}
