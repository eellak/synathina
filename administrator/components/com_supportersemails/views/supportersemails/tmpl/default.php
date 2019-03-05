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

$query = "SELECT * FROM #__team_donation_types
			WHERE published=1 AND parent_id=0 ORDER BY id ASC ";
$db->setQuery($query);
$donations = $db->loadObjectList();

$total = 0;

$from = '';
$to = '';
$donations_ids = [];
$donations_ids_sql = '';
$where = '';

$request = $_REQUEST;

if (isset($request['date_from']) && $request['date_from']) {
	$from = $request['date_from'];
	$where .= " AND date>='".$from." 00:00:00' ";
}
if (isset($request['date_to']) && $request['date_to']) {
	$to = $request['date_to'];
	$where .= " AND date<='".$to." 23:59:59' ";
}

if (isset($request['donations']) && $request['donations'] ) {
	foreach ($request['donations'] as $key => $donation) {
		if ($donation == 'on') {
			$donations_ids[] = $key;
		}
	}
	$donations_ids_sql = implode(',', $donations_ids);
	if ($donations_ids_sql) {
		$where .= " AND donations IN (".$donations_ids_sql.")";
	}
}

$query = "SELECT SUM(counter)
			FROM #__donations_counter
			WHERE id > 0 ".$where;
$db->setQuery($query);
$total = $db->loadResult();

?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#date_from" ).datepicker({ dateFormat: 'yy-mm-dd',minDate: new Date(2018, 2, 1) });
		$( "#date_to" ).datepicker({ dateFormat: 'yy-mm-dd',minDate: new Date(2018, 2, 1) });
  } );
  </script>
<form name="activities_stats" id="activities_stats" method="post"  action="index.php?option=com_supportersemails&view=supportersemails">
<h3>Φίλτρα</h3>
<h4>Ημερομηνίες</h4>
<p><label for="date_from">Ημερομηνία από:</label> <input style="cursor:default" readonly="true" value="<?=@$_REQUEST['date_from']?>" type="text" id="date_from" name="date_from" /><label for="date_to">Ημερομηνία έως:</label> <input style="cursor:default" readonly="true" value="<?=@$_REQUEST['date_to']?>" type="text" id="date_to" name="date_to" /></p>
<h4>Προσφορές:</h4>
<p>
<?php
	foreach($donations as $donation) {
		//get sub donations
		$query = "SELECT *
							FROM #__team_donation_types
							WHERE published=1 AND parent_id=".$donation->id." ORDER BY id ASC ";
		$db->setQuery($query);
		$donations_children = $db->loadObjectList();

		echo '<input class="input" '.(@$_REQUEST['donations'][$donation->id]=='on'?'checked="checked"':'').' type="checkbox" id="donation_'.$donation->id.'" name="donations['.$donation->id.']" />&nbsp;<label style="display:inline-block" for="donation_'.$donation->id.'">'.$donation->name.'</label>'.(count($donations_children) > 0 ? '<br />' : '&nbsp;&nbsp;&nbsp;&nbsp;');

		foreach ($donations_children as $donations_child) {
			echo '<input class="input" '.(@$_REQUEST['donations'][$donations_child->id]=='on'?'checked="checked"':'').' type="checkbox" id="donation_'.$donations_child->id.'" name="donations['.$donations_child->id.']" />&nbsp;<label style="display:inline-block" for="donation_'.$donations_child->id.'">'.$donations_child->name.'</label>&nbsp;&nbsp;&nbsp;';
		}
		echo (count($donations_children) > 0 ? '<hr /><br />' : '');
	}
?>
	<br /><br />
</p>
<input type="submit" class="btn btn-primary" name="submit" value="Προβολή" />
</form>

<?php
	if(@$_REQUEST['submit']){
		echo '<h2>Αποτελέσματα αναζήτησης</h2>';
		echo '<h4>Αριθμός αιτημάτων: '.(!$total ? 0 : $total).'</h4>';
	}
?>
