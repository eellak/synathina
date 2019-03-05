<?php

defined('_JEXEC') or die;

/**
 * Search Component Search Model
 *
 * @since  1.5
 */
class OpencallsModelOpencalls extends JModelLegacy
{
	/**
	 * Search data array
	 *
	 * @var array
	 */
	protected $opencalls = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	 
	
	public function __construct()
	{
	parent::__construct();
	// Set the pagination request variables
	}	 
	

	 
	public function getOpencalls(){
		//db connection
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = "SELECT * FROM #__content WHERE catid=15 AND state=1 AND created_by='".$user->id."' ORDER BY id DESC ";
		$db->setQuery( $query );
		$opencalls = $db->loadObjectList();	
		return $opencalls;
	}
}
