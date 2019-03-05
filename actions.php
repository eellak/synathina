<?php
header('Content-type: application/json; charset=UTF-8');
/**
 * @package    Core.Site
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Core');
}

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->setStart($startTime, $startMem)->mark('afterLoad') : null;
date_default_timezone_set('Europe/Athens');

// Instantiate the application.
$app = JFactory::getApplication('site');
$config = JFactory::getConfig();

$currentlang=@$_REQUEST['lang'];

//local db
$db = JFactory::getDbo();

//remote dbs
JLoader::registerPrefix('Remotedb', JPATH_BASE . '/remotedb');

//remote db
$dbRemoteClass = new RemotedbConnection();
$db_remote = $dbRemoteClass->remoteConnect();

$query = "SELECT * FROM #__team_activities WHERE published=1 ";
$db->setQuery($query);
$activities = $db->loadObjectList();
$activities_array_text=array();
foreach($activities as $activity){
	$activities_array_text[$activity->id]=$activity->name;
}

$date_now=date('Y-m-d').' 00:00:00';

//common db

//all actions
$fields = ['a.*', 'aa.subtitle', 'aa.address AS aaddress', 'aa.activities AS aactivities', 'aa.action_date_start AS aaction_date_start', 'aa.action_date_end AS aaction_date_end', 'aa.lat AS alat', 'aa.lng AS alng'];
$where = "aa.action_date_end>='".$date_now."' AND a.published='1' AND a.action_id=0";
$order_by = "aa.lat DESC";
$activityClass = new RemotedbActivity();
$actions = $activityClass->getActivitiesSubactivities($fields, $where, $order_by);

$i=0;
$data= '{
      "type": "FeatureCollection",
      "features": [';
foreach($actions as $action){
	//get live site url
	$live_site = $config->get('live_site');
	if ($action->origin == 2) {
		$live_site = $config->get('remote_site');
	}
	//get team
	if ($action->origin == 1) {
		$query = "SELECT t.id, t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
					WHERE t.id='".$action->team_id."' LIMIT 1";
		$db->setQuery($query);
		$team = $db->loadObject();
	} else {
		$query = "SELECT t.id, t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
					WHERE t.id='".$action->accmr_team_id."' LIMIT 1";
		$db_remote->setQuery($query);
		$team = $db_remote->loadObject();
	}

	$partners=explode(',',$action->partners);
	$partners_array=array_filter($partners);
	if(count($partners_array)>0){
		if($currentlang=='en'){
			$members='members';
		}else{
			$members='μέλη';
		}
	}else{
		if($currentlang=='en'){
			$members='member';
		}else{
			$members='μέλος';
		}
	}
	$sponsors_array=@explode(',',$action->supporters);
	$sponsor_id = @reset($sponsors_array);
	if($sponsor_id>0){
		if ($action->origin == 1) {
			$query = "SELECT logo FROM #__teams WHERE id='".$sponsor_id."' ";
			$db->setQuery($query);
			$sponsor_logo = $live_site . '/' . $db->loadResult();
		} else {
			$query = "SELECT logo FROM #__teams WHERE id='".$sponsor_id."' ";
			$db_remote->setQuery($query);
			$sponsor_logo = $live_site . '/' . $db_remote->loadResult();
		}
	}else{
		$sponsor_logo='';
	}
	$activities_array=explode(',',$action->aactivities);
	$activities_ids='[';
	for($a=0; $a<count($activities_array); $a++){
		if($activities_array[$a]!=''){
			$activities_ids.=''.$activities_array[$a].',';
		}
	}
	$activities_ids=rtrim($activities_ids,',').']';

	if($action->alat==''){
		$action->alat=0;
	}
	if($action->alng==''){
		$action->alng=0;
	}
	if($i%2==0){
		$cat=1;
	}else{
		$cat=2;
	}
	$i++;
	$date_array_start=explode(' ',$action->aaction_date_start);
	$date_array_start1=explode('-',$date_array_start[0]);
	$time_array_start=explode(':',$date_array_start[1]);
	$new_start_date=$date_array_start1[2].'-'.$date_array_start1[1].'-'.$date_array_start1[0].', '.$time_array_start[0].':'.$time_array_start[1];
	$date_array_end=explode(' ',$action->aaction_date_end);
	$date_array_end1=explode('-',$date_array_end[0]);
	$time_array_end=explode(':',$date_array_end[1]);
	$new_end_date=$date_array_end1[2].'-'.$date_array_end1[1].'-'.$date_array_end1[0].', '.$time_array_end[0].':'.$time_array_end[1];
	if($date_array_end1[2].'-'.$date_array_end1[1].'-'.$date_array_end1[0] == $date_array_start1[2].'-'.$date_array_start1[1].'-'.$date_array_start1[0]){
		$dates=$date_array_end1[2].'-'.$date_array_end1[1].'-'.$date_array_end1[0].', '.$time_array_start[0].':'.$time_array_start[1].' - '.$time_array_end[0].':'.$time_array_end[1];
	}else{
		$dates=$new_start_date.' - '.$new_end_date;
	}

	if($currentlang=='en'){
		$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->id.':'.$action->alias.'&Itemid=148');
	}else{
		$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->id.':'.$action->alias.'&Itemid=138');
	}
	if ($action->origin == 1) {
		$link_team = JRoute::_('index.php?option=com_teams&view=team&id='.$team->id.':'.$team->talias.'&Itemid=140');
	} else {
		$link_team = $live_site . '/index.php?option=com_teams&view=team&id='.$team->id.'&Itemid=140';
	}
	$data.= '{
            "type": "Point",
            "object_constructor": "Activity",
            "coordinates": ['.$action->alat.', '.$action->alng.'],
            "id": '.($i-1).',"action_id": '.$action->id.',
            "is_featured": false,
            "slug": "'.htmlspecialchars($action->alias).'","url": "'.htmlspecialchars($link).'","team_url": "'.htmlspecialchars($link_team).'",
            "category_id": '.$activities_ids.',
            "category_name" : "'.str_replace(array("\r\n","\r"),"",@$activities_array_text[$activities_array[0]]).'",
            "team_id": "'.$team->id.'",
            "team_name": "'.htmlspecialchars($team->tname).'",
            "team_members": "<span style=\'font-size:28px\'>'.(count($partners_array)+1).'</span><br />'.$members.'","address": "'.trim(htmlspecialchars($action->aaddress)).'",
            "sponsor_title": "","date": "'.$action->aaction_date_start.'","date_end": "'.$action->aaction_date_end.'","dates": "'.$dates.'",
            "title": "'.trim(str_replace(array("\r\n","\r"),"",htmlspecialchars($action->subtitle))).'",
            "content": "'.str_replace(array("\r\n","\r"),"",htmlspecialchars($action->short_description)).'",
            "content_img": "'.$live_site.'/images/actions/main_images/'.$action->image.'",
            "logo": "","logo_sponsor": "'.$sponsor_logo.'","logo_team": "'.$live_site.'/'.$team->tlogo.'"
      },';
}
$data = rtrim($data, ',');
$data.= ']}';

echo $data;
//echo $currentlang;
//echo str_replace(array("\r\n","\r"),"",$data);
