<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
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

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

//connect to db
$db = JFactory::getDBO();

$lang = JFactory::getLanguage();

$user = JFactory::getUser();

date_default_timezone_set('Europe/Athens');
//$time_diff=3600*3;
$time_diff=0;
$half_hour=3600/2;

if(@$_REQUEST['stegi_date']!=''){

	//$date = new DateTime("2016-06-20 09:00:00");
	// false
	//echo $date->getTimestamp()+60*60*3;
	//get activities
	/*$query = "SELECT t.name AS tname, a.id, a.subtitle, a.action_date_start, a.action_date_end FROM #__actions AS a INNER JOIN #__teams AS t ON t.id=a.team_id WHERE a.published=1 AND a.stegi_use=1 AND a.action_id>0 ORDER BY a.action_date_start ASC ";
	$db->setQuery($query);
	$actions = $db->loadObjectList();


	foreach($actions as $action){
		$start=$action->action_date_start;
		$start_array=explode(':',$start);
		$new_start=$start_array[0]; //2016-05-27 12
		$new_start_array=explode(' ',$new_start);
		$new_start_time=$new_start_array[1]; //12
		$end=$action->action_date_end;
		$end_array=explode(':',$end);
		$new_end=$end_array[0]; //2016-05-27 12
		$new_end_array=explode(' ',$new_end);
		$new_end_time=$new_end_array[1]; //12
		if($new_start_time<$new_end_time){
			for($i=$new_start_time; $i<$new_end_time; $i++){
				$actions_array[]=array($action->id, $action->subtitle, $new_start_array[0].' '.$i, $action->tname);
				$actions_date_array[]=$new_start_array[0].' '.$i;
				//$actions_date_array1[]=$new_start_array[0];
			}
		}
	}*/
	//get stegihours
	$actions_date_array=array();
	$actions_array=array();

	$query = "SELECT t.name AS tname, t.id AS tid, t.alias AS talias, a.id, a.name, a.alias, a.action_id, a.date_start, a.date_end FROM #__stegihours AS a
				INNER JOIN #__teams AS t
				ON t.id=a.team_id
				WHERE a.published=1
				ORDER BY a.date_start ASC";
	$db->setQuery($query);
	$actions = $db->loadObjectList();

	$start1 = time();
	//echo $start1."<br />";
	foreach($actions as $action){
		$start=$action->date_start;
		$date_start=new DateTime($action->date_start);
		$time_start=$date_start->getTimestamp()+$time_diff;
		$start_array=explode(':',$start);
		$new_start=$start_array[0]; //2016-05-27 12
		$new_start_array=explode(' ',$new_start);
		$new_start_time=intval($new_start_array[1]); //12
		$end=$action->date_end;
		$date_end=new DateTime($action->date_end);
		$time_end=$date_end->getTimestamp()+$time_diff;
		$end_array=explode(':',$end);
		$new_end=$end_array[0]; //2016-05-27 12
		if($end_array[1]!='00'){
			$time_end=$time_end+(60-$end_array[1])*60;
		}
		$new_end_array=explode(' ',$new_end);
		$new_end_time=intval($new_end_array[1]); //12
		//echo $action->id.' '.$time_end.'<br />';
		for($i=$time_start; $i<$time_end; $i+=3600){
			// if($action->action_id>0){
			// 	$fields = ['subtitle'];
			// 	$where = "action_id='".$action->action_id."'";
			// 	$limit = 1;
			// 	$subaction = $activityClass->getActivity($fields, $where, $limit);

			// 	if ($subaction->subtitle) {
			// 		$action->name = $subaction->subtitle;
			// 	}
			// }
			$actions_array[]=array($action->id, $action->name, date('Y-m-d H',$i), $action->tname, $action->action_id, $action->tid, $action->alias, $action->talias);
			$actions_date_array[]=date('Y-m-d H',$i);
			//echo $action->id.' '.$i.' '.date('Y-m-d H',$i).'<br />';
		}

		if($new_end_array[0]>@$_REQUEST['stegi_date'] && $new_end_array[0]>$new_start_array[0]  ){
			$new_end_time=24;
		}
		//if($new_start_time<$new_end_time){
			//echo $new_start_time.' --- '.$new_end_time.'<br />';
			//for($i=$new_start_time; $i<$new_end_time; $i++){
				//$actions_array[]=array($action->id, $action->name, $new_start_array[0].' '.($i<10?'0'.$i:$i), $action->tname);
				//$actions_date_array[]=$new_start_array[0].' '.($i<10?'0'.$i:$i);
			//}
		//}
	}
?>
<div class="c-diary">
		<h2 style="padding-left:20px">Ημερολόγιο στέγης</h2>
		<ul style="padding-left:20px" class="inline-list inline-list--separated inline-list--headlines">
				<!--<li class="selected"><a href="">Ημέρα</a></li>-->
		</ul>
		<div class="diary-switcher">
				<div class="is-block diary-labels">
<?php

	$req_date_array=explode('-',@$_REQUEST['stegi_date']);
	$req_timestamp=mktime('0','0','0',$req_date_array[1],$req_date_array[2],$req_date_array[0])+$time_diff;
	$days=array(1=>'Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο','Κυριακή');
	$months=array(1=>'Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');

	//$one_day=3600*24;
	//$new_time=time()+$time_diff+$i*$one_day;
	echo '<span '.($i==0?'class="active"':'').'>'.$days[date('N',$req_timestamp)].' '.date('d',$req_timestamp).' '.$months[date('n',$req_timestamp)].' '.date('Y',$req_timestamp).'</span>';
?>
				</div>
		</div>
		<div class="module module--synathina">
			<div class="module-skewed module-skewed--gray" rel="js-container">
<?php
		$new_time=$req_timestamp+$time_diff;
		//2016-05-27 12:00:00
		$new_date=date('Y',$new_time).'-'.date('m',$new_time).'-'.date('d',$new_time).' ';
		echo '<div id="tab-'.($i+1).'" class="tab '.($i==0?'active':'').'">';
		echo '	<div class="diary">
							<table class="diary-table">
								<colgroup>
									<col style="width: 50px;">
									<col style="width: calc(100% - 50px);">
								</colgroup>
								<tbody>';
		for($t=0; $t<13; $t++){
			$new_time1=$new_date.($t<10?0:'').$t;
			echo '			<tr>
										<td>
											'.$t.' '.($t==12?'μμ':'πμ').'
										</td>
										<td>';
			$found=array_keys($actions_date_array,$new_time1);

			if(!empty($found)){
				for($k=0; $k<count($found); $k++){
					$link=JRoute::_('index.php?option=com_actions&view=action&id='.$actions_array[$found[$k]][4].':'.$actions_array[$found[$k]][6].'&Itemid=138');
					$link_team=JRoute::_('index.php?option=com_teams&view=team&id='.$actions_array[$found[$k]][5].':'.$actions_array[$found[$k]][7].'&Itemid=140');
					echo '<div class="is-inline-block">
									<ul class="inline-list inline-list--separated inline-list--orange">
											<li>'.($actions_array[$found[$k]][4]>0?'<a href="'.$link.'" target="_blank">':'').$actions_array[$found[$k]][1].($actions_array[$found[$k]][4]>0?'</a>':'').'</li>
											<li><a href="'.$link_team.'" target="_blank">'.$actions_array[$found[$k]][3].'</a></li>
									</ul>
								</div>';
				}
			}
			echo '				</td>
									</tr>';
		}
		for($t=1; $t<12; $t++){
			$new_time1=$new_date.($t+12);
			echo '			<tr>
										<td>
											'.$t.' μμ
										</td>
										<td>';
			$found=array_keys($actions_date_array,$new_time1);
			if(!empty($found)){
				for($k=0; $k<count($found); $k++){
					$link=JRoute::_('index.php?option=com_actions&view=action&id='.$actions_array[$found[$k]][4].':'.$actions_array[$found[$k]][6].'&Itemid=138');
					$link_team=JRoute::_('index.php?option=com_teams&view=team&id='.$actions_array[$found[$k]][5].':'.$actions_array[$found[$k]][7].'&Itemid=140');
					echo '<div class="is-inline-block">
									<ul class="inline-list inline-list--separated inline-list--orange">
											<li>'.($actions_array[$found[$k]][4]>0?'<a href="'.$link.'" target="_blank">':'').$actions_array[$found[$k]][1].($actions_array[$found[$k]][4]>0?'</a>':'').'</li>
											<li><a href="'.$link_team.'" target="_blank">'.$actions_array[$found[$k]][3].'</a></li>
									</ul>
								</div>';
				}
			}
			echo '				</td>
									</tr>';
		}
		echo '			</tbody>
							</table>
						</div>';
		echo '</div>';
?>
			</div>
		</div>

	</div>
<?php
}else{
	//header('Location:index.php');
	//exit;
}
?>