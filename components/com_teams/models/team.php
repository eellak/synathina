<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Teams Model
 */
class TeamsModelTeam extends JModelItem
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
	public function getTable($type = 'Teams', $prefix = 'Teams', $config = array())
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

	public function getTeam()
	{
		$db = JFactory::getDBO();

		$query = "SELECT *
					FROM #__teams
					WHERE id='".@$_REQUEST['id']."' AND published=1 LIMIT 1";
		$db->setQuery( $query );

		return $db->loadObject();
	}

	public function getTeamFiles()
	{
		$db = JFactory::getDBO();

		$query = "SELECT path
					FROM #__team_files
					WHERE team_id='".@$_REQUEST['id']."' ";
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	public function getActivitiesSupport()
	{
		$activityClass = new RemotedbActivity();

		$fields = ['name', 'alias', 'image', 'id'];
		$where = "published=1 AND action_id=0 AND find_in_set('".@$_REQUEST['id']."',supporters) AND team_id!='".@$_REQUEST['id']."'";
		$order_by = "id DESC";

		return $activityClass->getActivities($fields, $where, $order_by);
	}

	public function getActivitiesTeam()
	{
		$activityClass = new RemotedbActivity();

		$fields = ['name', 'alias', 'image', 'id'];
		$where = "published=1 AND action_id=0 AND team_id='".@$_REQUEST['id']."'";
		$order_by = "id DESC";

		return $activityClass->getActivities($fields, $where, $order_by);
	}

}
