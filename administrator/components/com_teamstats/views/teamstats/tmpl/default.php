<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamstats
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');


$db = JFactory::getDBO();

$where='';

//request
if(@$_REQUEST['date_from']!=''){
	$from_array=explode('/',@$_REQUEST['date_from']);
	$new_from=$from_array[2].'-'.$from_array[1].'-'.$from_array[0].' 00:00:00';
	$where.=" AND t.created>='".$new_from."' ";
}
if(@$_REQUEST['date_to']!=''){
	$to_array=explode('/',@$_REQUEST['date_to']);
	$new_to=$to_array[2].'-'.$to_array[1].'-'.$to_array[0].' 23:59:59';
	$where.=" AND t.created<='".$new_to."' ";
}

if(@$_REQUEST['team']=='on'){
	$where.=" AND t.create_actions=1 ";
}
if(@$_REQUEST['supporter']=='on'){
	$where.=" AND t.support_actions=1 ";
}

$query = "SELECT t.*
					FROM #__teams AS t
					INNER JOIN #__users AS u ON u.id=t.user_id
					WHERE u.block=0 AND u.activation='' AND t.published=1 ".$where." ORDER BY t.created DESC ";

//echo $query;
//die;
$db->setQuery( $query );
$teams = $db->loadObjectList();
$total = count($teams);


//print_r(@$_REQUEST);
?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#date_from" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#date_to" ).datepicker({ dateFormat: 'dd/mm/yy' });
  } );
  </script>
<form name="activities_teamstats" id="activities_teamstats" method="post"  action="index.php?option=com_teamstats&view=teamstats">
<h3>Ημερομηνίες</h3>
<p>Ημερομηνία από: <input value="<?=@$_REQUEST['date_from']?>" type="text" id="date_from" name="date_from" />&nbsp;&nbsp;&nbsp;&nbsp;Ημερομηνία έως: <input value="<?=@$_REQUEST['date_to']?>" type="text" id="date_to" name="date_to" /></p>
<h3>Φίλτρα</h3>
<p>
	<input class="input" <?=@$_REQUEST['team']=='on'?'checked="checked"':''?> type="checkbox" id="team" name="team" />&nbsp;<label style="display:inline-block" for="team">Διοργανωτής</label>&nbsp;&nbsp;+&nbsp;&nbsp;
	<input class="input" <?=@$_REQUEST['supporter']=='on'?'checked="checked"':''?> type="checkbox" id="supporter" name="supporter" />&nbsp;<label style="display:inline-block" for="supporter">Υποστηρικτής</label>
</p>
<input type="submit" class="btn btn-primary"  name="submit" value="Προβολή" />
</form>

<?php
	if(@$_REQUEST['submit']){
		echo '<h2>Αποτελέσματα αναζήτησης</h2>';
		echo '<h4>Αριθμός ομάδων: '.$total.'</h4>';
	}
?>
