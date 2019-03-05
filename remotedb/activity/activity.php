<?php

class RemotedbActivity {

	protected $options;

	public function __construct()
	{
		//remote db
		$db_remote_class = new RemotedbConnection();
		$this->db_remote = $db_remote_class->connect();
	}

	// GET Activity
	public function getActivity($fields = [], $where = '', $limit = '', $as_array = false)
	{
		$query_fields = '*';
		if ($fields) {
			$query_fields = implode(',', $fields);
		}

		$query_where = '';
		if ($where) {
			$query_where = ' WHERE ' . $where;
		}

		$query_limit = '';
		if ($limit) {
			$query_limit = ' LIMIT ' . $limit;
		}

		$query = "SELECT ".$query_fields." FROM #__actions ".$query_where." ".$query_limit;

		$this->db_remote->setQuery($query);

		if ($as_array) {
			return $this->db_remote->loadAssoc();
		} else {
			return $this->db_remote->loadObject();
		}
	}

	// GET Activities or Subactivities
	public function getActivities($fields = [], $where = '', $order = '', $limit = '', $as_array = false)
	{
		$query_fields = '*';
		if ($fields) {
			$query_fields = implode(',', $fields);
		}

		if ($where) {
			$where = ' AND ' . $where;
		}

		if ($order) {
			$order = ' ORDER BY ' . $order;
		}

		if ($limit) {
			$limit = ' LIMIT ' . $limit;
		}

		$query = "SELECT ".$query_fields." FROM #__actions WHERE 1=1 ".$where." ".$order." ".$limit;

		$this->db_remote->setQuery($query);

		if ($as_array) {
			return $this->db_remote->loadAssocList();
		} else {
			return $this->db_remote->loadObjectList();
		}
	}

	// GET All Activities
	public function getActivitiesSubactivities($fields = [], $where = '', $order = '', $limit = '', $group_by = '', $as_array = false)
	{
		$query_fields = '*';
		if ($fields) {
			$query_fields = implode(',', $fields);
		}

		if ($where) {
			$where = ' AND ' . $where;
		}

		if ($group_by) {
			$group_by = ' GROUP BY ' . $group_by;
		}

		if ($order) {
			$order = ' ORDER BY ' . $order;
		}

		if ($limit) {
			$limit = ' LIMIT ' . $limit;
		}

		$query = "SELECT ".$query_fields." FROM #__actions AS a INNER JOIN #__actions AS aa ON a.id=aa.action_id WHERE (a.origin=1 OR (a.origin=2 AND a.remote=1)) ".$where." ".$group_by." ".$order." ".$limit;
		//echo str_replace('#__', 'cemyx_', $query);
		$this->db_remote->setQuery($query);

		if ($as_array) {
			return $this->db_remote->loadAssocList();
		} else {
			return $this->db_remote->loadObjectList();
		}
	}

	public function getActivitiesCount($where = '', $group_by = '') {

		if ($where) {
			$where = ' AND ' . $where;
		}
		$query = "SELECT COUNT(aa.id) FROM #__actions AS a
					INNER JOIN #__actions AS aa ON aa.action_id=a.id
					WHERE (a.origin=1 OR (a.origin=2 AND a.remote=1)) ".$where." ".$group_by." ";
		$this->db_remote->setQuery($query);

		return $this->db_remote->loadResult();
	}

	public function getActivitiesCountLimited($where = '', $group_by = '') {

		if ($where) {
			$where = ' WHERE ' . $where;
		}
		$query = "SELECT COUNT(aa.id) FROM #__actions AS a
					INNER JOIN #__actions AS aa ON aa.action_id=a.id
					".$where." ".$group_by." ";
		$this->db_remote->setQuery($query);

		return $this->db_remote->loadResult();
	}

}