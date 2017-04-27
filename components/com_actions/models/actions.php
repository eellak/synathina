<?php

defined('_JEXEC') or die;

/**
 * Search Component Search Model
 *
 * @since  1.5
 */
class ActionsModelActions extends JModelLegacy
{
	/**
	 * Search data array
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * Search total
	 *
	 * @var integer
	 */
	protected $_total = null;

	/**
	 * Search areas
	 *
	 * @var integer
	 */
	protected  $_areas = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */


	public function __construct()
	{
	parent::__construct();
	// Set the pagination request variables
	$this->setState('limit', JRequest::getVar('limit', 5, '', 'int'));
	$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
	//$this->setState('dennis', 1);
	//echo JRequest::getVar('limitstart');
	}



	public function getMsg(){
		//echo '<pre>';
		//print_r(@$_REQUEST);
		//echo '</pre>';
		//db connection
		$db = JFactory::getDBO();
		$config= new JConfig();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		//requests
		if(@$_REQUEST['action_limit']>0){
			$action_limit=$_REQUEST['action_limit'];
		}else{
			$action_limit=6;
		}
		$where='';
		$or_sql='';
		$or_sql1='';
		//request
		if(@$_REQUEST['from']!=''){
			$from_array=explode('/',@$_REQUEST['from']);
			//print_r($from_array);
			$new_from=$from_array[2].'-'.$from_array[1].'-'.$from_array[0].' 00:00:00';
			$where.=" AND aa.action_date_start>='".$new_from."' ";
		}
		if(@$_REQUEST['to']!=''){
			$to_array=explode('/',@$_REQUEST['to']);
			$new_to=$to_array[2].'-'.$to_array[1].'-'.$to_array[0].' 23:59:59';
			$where.=" AND aa.action_date_end<='".$new_to."' ";
		}
		if(@$_REQUEST['search_name']!=''){
			$where.=" AND (a.name LIKE '%".trim(@$_REQUEST['search_name'])."%' OR aa.subtitle LIKE '%".trim(@$_REQUEST['search_name'])."%' OR t.name LIKE '%".trim(@$_REQUEST['search_name'])."%') ";
		}
		if(@$_REQUEST['best']=='on'){
			$where.=" AND a.best_practice=1 ";
		}
		//areas
		$or=0;
		for($i=1; $i<8; $i++){
			if(@$_REQUEST['area'.$i]=='on'){
				$or=1;
			}
		}
		if($or==1){
			$or_sql=" AND (";
			for($i=1; $i<8; $i++){
				if(@$_REQUEST['area'.$i]=='on'){
					$or_sql.="aa.area='".$i."' OR ";
				}
			}
			$or_sql=rtrim($or_sql,'OR ').")";
		}
		//activities
		$or1=0;
		for($i=1; $i<20; $i++){
			if(@$_REQUEST['activity'.$i]=='on'){
				$or1=1;
			}
		}
		if($or1==1){
			$or_sql1=" AND (";
			for($i=1; $i<20; $i++){
				if(@$_REQUEST['activity'.$i]=='on'){
					//$or_sql1.="aa.activity='".$i."' OR ";
					$or_sql1.=" find_in_set('".$i."',aa.activities) OR ";
				}
			}
			$or_sql1=rtrim($or_sql1,'OR ').")";
		}
		$query="SELECT aa.*, a.alias, a.short_description AS short, a.best_practice, a.id AS aid, a.image AS aimage, a.published as apublished FROM #__actions AS aa INNER JOIN #__teams AS t ON aa.team_id=t.id INNER JOIN #__actions AS a ON aa.action_id=a.id WHERE a.id>0 ".$where." ".$or_sql." ".$or_sql1." AND aa.action_id>0 ".($isroot==1?'AND a.published>=0':'AND a.published=1')." AND t.published=1 ORDER BY aa.action_date_start ASC ";
		//echo $query;
		//die;
		$db->setQuery( $query );
		$actions = $db->loadObjectList();
		$this->_total = count($actions);
		$this->items = array_splice($actions, $this->getState('limitstart'), $action_limit);

		return $this->items;
	}

	public function getActivities(){
		//db connection
		$db = JFactory::getDBO();
		$query="SELECT * FROM #__team_activities WHERE published=1 ORDER BY name ASC";
		$db->setQuery( $query );
		$activities = $db->loadObjectList();
		return $activities;
	}

	public function getBestpractices(){
		$lang = JFactory::getLanguage();
		$this->language = $lang->getTag();//$doc->language;
		$lang_code_array=explode('-',$this->language);
		$lang_code=$lang_code_array[0];
		//db connection
		$db = JFactory::getDBO();
		$query="SELECT c.*,t.name AS tname FROM #__content AS c
				INNER JOIN #__users AS u ON u.id=c.created_by
				INNER JOIN #__teams AS t ON t.user_id=u.id
				WHERE c.catid=".($lang_code=='el'?'20':'21')." AND state=1 ORDER BY c.ordering ASC";
		$db->setQuery( $query );
		$bestpractices = $db->loadObjectList();
		return $bestpractices;
	}
	public function getPagination()
			 {
				if(@$_REQUEST['action_limit']>0){
					$action_limit=$_REQUEST['action_limit'];
				}else{
					$action_limit=6;
				}
			$app    = JFactory::getApplication();
			$router = $app->getRouter();
		if(@$_REQUEST['from']!=''){
			$router->setVar( 'from', @$_REQUEST['from'] );
		}
		if(@$_REQUEST['to']!=''){
			$router->setVar( 'to', @$_REQUEST['to'] );
		}
		if(@$_REQUEST['search_name']!=''){
			$router->setVar( 'search_name', @$_REQUEST['search_name'] );
		}
		if(@$_REQUEST['best']=='on'){
			$router->setVar( 'best', @$_REQUEST['best'] );
		}
		for($i=1; $i<8; $i++){
			if(@$_REQUEST['area'.$i]=='on'){
				$router->setVar( 'area'.$i, @$_REQUEST['area'.$i] );
			}
		}
		for($i=1; $i<20; $i++){
			if(@$_REQUEST['activity'.$i]=='on'){
				$router->setVar( 'activity'.$i, @$_REQUEST['activity'.$i] );
			}
		}
			 jimport('joomla.html.pagination');
			 $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $action_limit );
			 return $this->_pagination;
	}
	/**
	 * Method to set the search parameters
	 *
	 * @param   string  $keyword   string search string
	 * @param   string  $match     matching option, exact|any|all
	 * @param   string  $ordering  option, newest|oldest|popular|alpha|category
	 *
	 * @return  void
	 *
	 * @access	public
	 */
	public function setSearch($keyword, $match = 'all', $ordering = 'newest')
	{
		if (isset($keyword))
		{
			$this->setState('origkeyword', $keyword);

			if ($match !== 'exact')
			{
				$keyword = preg_replace('#\xE3\x80\x80#s', ' ', $keyword);
			}

			$this->setState('keyword', $keyword);
		}

		if (isset($match))
		{
			$this->setState('match', $match);
		}

		if (isset($ordering))
		{
			$this->setState('ordering', $ordering);
		}
	}

	/**
	 * Method to get weblink item data for the category
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$areas = $this->getAreas();

			JPluginHelper::importPlugin('search');
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger('onContentSearch', array(
				$this->getState('keyword'),
				$this->getState('match'),
				$this->getState('ordering'),
				$areas['active'])
			);

			$rows = array();

			foreach ($results as $result)
			{
				$rows = array_merge((array) $rows, (array) $result);
			}

			$this->_total	= count($rows);

			if ($this->getState('limit') > 0)
			{
				$this->_data = array_splice($rows, $this->getState('limitstart'), $this->getState('limit'));
			}
			else
			{
				$this->_data = $rows;
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of weblink items for the category
	 *
	 * @access public
	 * @return  integer
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to set the search areas
	 *
	 * @param   array  $active  areas
	 * @param   array  $search  areas
	 *
	 * @return  void
	 *
	 * @access	public
	 */
	public function setAreas($active = array(), $search = array())
	{
		$this->_areas['active'] = $active;
		$this->_areas['search'] = $search;
	}

	/**
	 * Method to get a pagination object of the weblink items for the category
	 *
	 * @access public
	 * @return  integer
	 */


	/**
	 * Method to get the search areas
	 *
	 * @return int
	 *
	 * @since 1.5
	 */
	public function getAreas()
	{
		// Load the Category data
		if (empty($this->_areas['search']))
		{
			$areas = array();

			JPluginHelper::importPlugin('search');
			$dispatcher = JEventDispatcher::getInstance();
			$searchareas = $dispatcher->trigger('onContentSearchAreas');

			foreach ($searchareas as $area)
			{
				if (is_array($area))
				{
					$areas = array_merge($areas, $area);
				}
			}

			$this->_areas['search'] = $areas;
		}

		return $this->_areas;
	}
}
