<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');


$db = JFactory::getDBO();

$remoteClass = new RemotedbConnection();
$db_remote = $remoteClass->connect();

$query = "SELECT *
					FROM #__team_activities
					WHERE published=1 ORDER BY id ASC ";

$db->setQuery($query);
$thematikes = $db->loadObjectList();

$query = "SELECT *
					FROM #__team_donation_types
					WHERE published=1 AND parent_id=0 ORDER BY id ASC ";
$db->setQuery($query);
$donations = $db->loadObjectList();

$where='';
$or_sql='';
$or_sql1='';
$or_sql2='';
$or_sql3='';
$or_sql4='';
$where_stegi='';
//request
if(@$_REQUEST['date_from']!=''){
	$from_array=explode('/',@$_REQUEST['date_from']);
	$new_from=$from_array[2].'-'.$from_array[1].'-'.$from_array[0].' 00:00:00';
	$where.=" AND aa.action_date_start>='".$new_from."' ";
	$where_stegi.=" AND date_start>='".$new_from."' ";
}
if(@$_REQUEST['date_to']!=''){
	$to_array=explode('/',@$_REQUEST['date_to']);
	$new_to=$to_array[2].'-'.$to_array[1].'-'.$to_array[0].' 23:59:59';
	$where.=" AND aa.action_date_start<='".$new_to."' ";
	$where_stegi.=" AND date_start<='".$new_to."' ";
}

//areas
$or=0;
for($i=1; $i<8; $i++){
	if(@$_REQUEST['koinotita_'.$i]=='on'){
		$or=1;
	}
}
if($or==1){
	$or_sql=" AND (";
	for($i=1; $i<8; $i++){
		if(@$_REQUEST['koinotita_'.$i]=='on'){
			$or_sql.="aa.area='".$i."' OR ";
		}
	}
	$or_sql=rtrim($or_sql,'OR ').")";
}
//activities
$or1=0;
for($i=1; $i<20; $i++){
	if(@$_REQUEST['thematiki_'.$i]=='on'){
		$or1=1;
	}
}
if($or1==1){
	$or_sql1=" AND (";
	for($i=1; $i<20; $i++){
		if(@$_REQUEST['thematiki_'.$i]=='on'){
			//$or_sql1.="aa.activity='".$i."' OR ";
			$or_sql1.=" find_in_set('".$i."',aa.activities) OR ";
		}
	}
	$or_sql1=rtrim($or_sql1,'OR ').")";
}
if(@$_REQUEST['stegi_use']==1){
	$or_sql2=" AND aa.stegi_use=1 ";
}
if(@$_REQUEST['municipality_use']==1){
	$or_sql3=" AND a.municipality_services!='' ";
}
//donations
$or2=0;
for($i=1; $i<40; $i++){
	if(@$_REQUEST['donation_'.$i]=='on'){
		$or2=1;
	}
}
if($or2==1){
	$or_sql4=" AND (";
	foreach($donations as $donation){
		if(@$_REQUEST['donation_'.$donation->id]=='on'){
			$or_sql4.=" find_in_set('".$donation->id."',a.org_donation) OR ";
		}
	}
	$or_sql4=rtrim($or_sql4,'OR ').")";
}
$query="SELECT aa.id
				FROM #__actions AS aa INNER JOIN #__actions AS a ON aa.action_id=a.id WHERE a.id>0 AND a.origin=1
				".$where." ".$or_sql." ".$or_sql1." ".$or_sql2." ".$or_sql3." ".$or_sql4."
				AND aa.action_id>0 AND a.published=1
				ORDER BY aa.action_date_start DESC ";
//previous inner withour remote actions
//INNER JOIN #__teams AS t ON aa.team_id=t.id

$db_remote->setQuery( $query );
$actions = $db_remote->loadObjectList();
$total = count($actions);

$query="SELECT id
				FROM #__stegihours
				WHERE id>0
				".$where_stegi."
				AND action_id=0 AND published=1";
$db->setQuery( $query );
$actions_stegi = $db->loadObjectList();
$total_stegi = count($actions_stegi);

//print_r(@$_REQUEST);
?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#date_from" ).datepicker({ dateFormat: 'dd/mm/yy',minDate: new Date(2017, 0, 1) });
		$( "#date_to" ).datepicker({ dateFormat: 'dd/mm/yy',minDate: new Date(2017, 0, 1) });
  } );
  </script>
<form name="activities_stats" id="activities_stats" method="post"  action="index.php?option=com_stats&view=stats">
<h3>Ημερομηνίες</h3>
<p><label for="date_from">Ημερομηνία από:</label> <input style="cursor:default" readonly="true" value="<?=@$_REQUEST['date_from']?>" type="text" id="date_from" name="date_from" /><label for="date_to">Ημερομηνία έως:</label> <input style="cursor:default" readonly="true" value="<?=@$_REQUEST['date_to']?>" type="text" id="date_to" name="date_to" /></p>
<h3>Φίλτρα</h3>
<h4>Θεματικές:</h4>
<p>
<?php
	foreach($thematikes as $thematiki){
		echo '<input class="input" '.(@$_REQUEST['thematiki_'.$thematiki->id]=='on'?'checked="checked"':'').' type="checkbox" id="thematiki_'.$thematiki->id.'" name="thematiki_'.$thematiki->id.'" />&nbsp;<label style="display:inline-block" for="thematiki_'.$thematiki->id.'">'.$thematiki->name.'</label>&nbsp;&nbsp;&nbsp;';
	}
?>
</p>
<h4>Κοινοτικά διαμερίσματα:</h4>
<p>
<?php
	for($k=1; $k<8; $k++){
		echo '<input class="input" '.(@$_REQUEST['koinotita_'.$k]=='on'?'checked="checked"':'').' type="checkbox" id="koinotita_'.$k.'" name="koinotita_'.$k.'" />&nbsp;<label style="display:inline-block" for="koinotita_'.$k.'">'.$k.'ο κοινοτικό διαμέρισμα</label>&nbsp;&nbsp;&nbsp;';
	}
?>
</p>
<h4>Χρήση στέγης:</h4>
<p>
	<select name="stegi_use">
		<option value="0" <?=(@$_REQUEST['stegi_use']==0||!isset($_REQUEST['stegi_use'])?'selected="selected"':'')?>>--</option>
		<option value="1" <?=(@$_REQUEST['stegi_use']==1?'selected="selected"':'')?>>ΝΑΙ</option>
	</select>
</p>
<h4>Προσφορές:</h4>
<p>
<?php
	foreach($donations as $donation){
		echo '<input class="input" '.(@$_REQUEST['donation_'.$donation->id]=='on'?'checked="checked"':'').' type="checkbox" id="donation_'.$donation->id.'" name="donation_'.$donation->id.'" />&nbsp;<label style="display:inline-block" for="donation_'.$donation->id.'">'.$donation->name.'</label>&nbsp;&nbsp;&nbsp;';
	}
?>
</p>
<h4>Ζήτηση Υποστήριξης από το δήμο:</h4>
<p>
	<select name="municipality_use">
		<option value="0" <?=(@$_REQUEST['municipality_use']==0||!isset($_REQUEST['municipality_use'])?'selected="selected"':'')?>>--</option>
		<option value="1" <?=(@$_REQUEST['municipality_use']==1?'selected="selected"':'')?>>ΝΑΙ</option>
	</select>
</p>
<input type="submit" class="btn btn-primary" name="submit" value="Προβολή" />
</form>

<?php
	if(@$_REQUEST['submit']){
		echo '<h2>Αποτελέσματα αναζήτησης</h2>';
		echo '<h4>Αριθμός δράσεων: '.$total.'</h4>';
		echo '<h4>Αριθμός Εσωτερικών Συναντήσεων: '.$total_stegi.'</h4>';
		echo '<br /><h3>Προηγούμενα έτη</h3>';
		echo '<h4>Έτος 2016 - Δράσεις πλατφόρμας: 638 - Δράσεις στη στέγη: 459</h4>';
		echo '<h4>Έτος 2015 - Δράσεις πλατφόρμας: 451 - Δράσεις στη στέγη: 169</h4>';
		echo '<h4>Έτος 2014 - Δράσεις πλατφόρμας: 317 - Δράσεις στη στέγη: 70</h4>';
		echo '<h4>Έτος 2013 - Δράσεις πλατφόρμας: 185 - Δράσεις στη στέγη: 23</h4>';
	}
?>
