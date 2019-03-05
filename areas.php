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

//get lang variables
$lang = @$_REQUEST['lang'];
if ($lang == 'en'){
} else {
	$lang='el';
}

// Instantiate the application.
$app = JFactory::getApplication('site');
$config = JFactory::getConfig();

//local db
$db = JFactory::getDbo();

//remote db
JLoader::registerPrefix('Remotedb', JPATH_BASE . '/remotedb');

//$areas_colors=array(1=>'fbee66','00ffca','dbacb9','24c2e9 ','dd9e58','e55229','cf93ff');
$areas_colors=array(1=>'F8E400','00FFCB','F79EB7','00C4F4 ','FF8700','E13200','C77FFF');

function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
        }
    }
    return 'th';
}
$up_to_2016=array(
    1=>1400,
    2=>136,
	3=>348,
	4=>58,
	5=>34,
	6=>252,
	7=>84
);
$up_to_2016_teams=array(
	1=>148,
	2=>16,
	3=>59,
	4=>9,
	5=>5,
	6=>40,
	7=>10
);

$activityClass = new RemotedbActivity();

echo '[';
for($i=1; $i<8; $i++){
    //remote db
    $where = "aa.published=1 AND a.published=1 AND aa.action_id>0 AND aa.area='".$i."' AND aa.action_date_start>='2017-01-01 00:00:00' AND aa.action_date_start<='".date('Y-m-d H:i:s')."'";
    $count = $activityClass->getActivitiesCount($where) + $up_to_2016[$i];

    $where = "a.accmr_team_id=0 AND a.origin=1 AND aa.published=1 AND a.published=1 AND aa.action_id>0 AND aa.area='".$i."' AND aa.action_date_start>='2017-01-01 00:00:00' AND aa.action_date_start<='".date('Y-m-d H:i:s')."'";
    $group_by = "GROUP BY a.team_id";
    $count_teams_local = $activityClass->getActivitiesCountLimited($where, $group_by);

    $where = "a.team_id=0 AND a.origin=2 AND a.remote=1 AND aa.published=1 AND a.published=1 AND aa.action_id>0 AND aa.area='".$i."' AND aa.action_date_start>='2017-01-01 00:00:00' AND aa.action_date_start<='".date('Y-m-d H:i:s')."'";
    $group_by = "GROUP BY a.accmr_team_id";
    $count_teams_remote = $activityClass->getActivitiesCountLimited($where, $group_by);

    $count_teams = $count_teams_local + $count_teams_remote + $up_to_2016_teams[$i];

	if($areas_colors[$i]!=''){
		echo '{
      "details" : {
        "name" : "'.$i.'a_diamerisma",
        "id" : "'.$i.'",
        "title" : "'.$i.($lang=='en'?ordinal_suffix($i):'η').' '.($lang=='en'?'District':'Δημοτική Κοινότητα').'",
        "population" : "<strong class=\"cross-side-title\">'.$i.'<sup>η</sup></strong> <br> '.($lang=='en'?'District':'Δημοτική Κοινότητα').'",
        "teams" : "'.$count_teams.' '.($lang=='en'?'Team'.($count_teams==1?'':'s'):'Oμάδ'.($count_teams==1?'α':'ες')).'",
        "activities": "'.$count.' '.($lang=='en'?'Activit'.($count_teams==1?'y':'ies'):'Δράσ'.($count_teams==1?'η':'εις')).'"
      },
      "kmlPath"  : "/templates/synathina/js_collections/maps/'.$i.'o_Diamerisma.kml",
      "styles" : {
         "strokeColor": "#'.$areas_colors[$i].'",
         "strokeOpacity": "1",
         "strokeWeight": "0",
         "fillColor": "#'.$areas_colors[$i].'",
         "fillOpacity": "0.6"
      }
    }'.($i==7?'':',');
	}

}
echo ']';

?>