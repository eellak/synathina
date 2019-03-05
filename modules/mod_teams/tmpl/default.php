<?php
defined('_JEXEC') or die;
//connect to db
$db = JFactory::getDBO();

//get menu item notes
$note='';
$query = "SELECT note FROM #__menu WHERE id='".@$_REQUEST['Itemid']."' ";
$db->setQuery($query);
$note = $db->loadResult();

//get activities
$query = "SELECT * FROM #__team_activities WHERE published=1 ORDER BY name ASC ";
$db->setQuery($query);
$activities = $db->loadObjectList();

//language
$doc = JFactory::getDocument();
$lang_code_array=explode('-',$doc->language);
$lang_code=$lang_code_array[0];

//get teams teams
$order = 0;
$where = ' published=1 AND create_actions=1 ';
if (count(@$_REQUEST['activities']) > 0) {
	$where .= ' AND (';
	foreach (@$_REQUEST['activities'] as $act) {
		$where .= ' find_in_set(\''.$act.'\',activities) OR ';
	}
	$where = substr($where, 0, -3);
	$where .= ') ';
	$order = 1;
}
if (@$_REQUEST['search_teams'] != '') {
	$where .= " AND name LIKE '%".trim(@$_REQUEST['search_teams'])."%' ";
	$order = 1;
}
$order_by = ($order == 1?'name ASC':'RAND()');

$query = "SELECT * FROM #__teams
			WHERE ".$where."
			ORDER BY ".$order_by;
$db->setQuery( $query );

$teams_teams = $db->loadObjectList();
$count_teams_teams = count($teams_teams);

//get teams supporters
$order = 0;
$where = ' hidden=0 AND published=1 AND support_actions=1 ';
if (count(@$_REQUEST['activities_s']) > 0) {
	$where .= ' AND (';
	foreach (@$_REQUEST['activities_s'] as $act) {
		$where .= ' find_in_set(\''.$act.'\',activities) OR ';
	}
	$where = substr($where, 0, -3);
	$where .= ') ';
	$order = 1;
}
if (@$_REQUEST['search_supporters'] != '') {
	$where .= " AND name LIKE '%".trim(@$_REQUEST['search_supporters'])."%' ";
	$order = 1;
}
$order_by = ($order == 1?'name ASC':'RAND()');

$query = "SELECT * FROM #__teams
			WHERE ".$where."
			ORDER BY ".$order_by;
$db->setQuery( $query );

$teams_supporters = $db->loadObjectList();
$count_teams_supporters = count($teams_supporters);

//anchors
$anchor='#teams-results';
if (count(@$_REQUEST['activities']) > 0 || @$_REQUEST['search_teams'] != '') {
	$anchor='#teams-results';
}

if (count(@$_REQUEST['activities_s']) > 0 || @$_REQUEST['search_supporters'] != '') {
	$anchor='#supporters-results';
}

?>
<script>
function clear_form(counter, id1, id2){
	for(i=1; i< parseInt(counter); i++){
		$("#"+id1+''+i).attr( "checked", false );
	}
	$("#"+id2).attr('value', '');
}
</script>
<?php if ($note == 'teams') { ?>
<h3 class="module-title module-title--layout" id="teams-results"><?php echo JText::_('TEAMS_TEAMS');?></h3>
<div class="filters">
	<form action="<?php echo JURI::current(); ?><?=$anchor?>" class="form" method="get" name="search_teams_form" id="search_teams_form">
		<div class="filter-search">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_TEAMS_PLACEHOLDER'); ?>" name="search_teams" id="search_teams" value="<?php echo strip_tags(@$_REQUEST['search_teams']);?>" />
			</div><!-- /input-group -->
		</div>
		<div class="filter-checkbox">
<?php
	$i=1;
	foreach($activities as $activity){
		echo '	<div class="form-group">
					<input id="box'.$i.'" type="checkbox" name="activities[activity_'.$activity->id.']" '.(@$_REQUEST['activities']['activity_'.$activity->id]==$activity->id?'checked="checked"':'').' value="'.$activity->id.'" />
					<label for="box'.$i.'" class="label-horizontal">'.($lang_code=='en'?$activity->name_en:$activity->name).'</label>
				</div>';
		$i++;
	}
?>

		</div>
		<div class="block"></div>
		<button type="submit" style="padding: 5px 10px;" class="pull-right btn btn--coral btn--bold btn btn-primary validate"><?=($lang_code=='en'?'SEARCH':'ΑΝΑΖΗΤΗΣΗ')?></button>
		<button type="reset" onclick="clear_form(<?=$i?>,'box','search_teams');" style="padding: 5px 10px; margin-right:10px;" class="pull-right btn btn--grey btn--bold btn btn-primary validate"><?=($lang_code=='en'?'RESET':'ΚΑΘΑΡΙΣΜΟΣ')?></button>
		<div class="block" style="clear:both"></div>
	</form>
</div>
<div class="module module--synathina">

	<div class="gallery gallery--multirow gallery--filter" rel="js-start-gallery">
<?php
	if(count($teams_teams)==0){
		if($lang_code=='en'){
			echo 'No results';
		}else{
			echo 'Δε βρέθηκαν αποτελέσματα αναζήτησης.';
		}
	}
	$c=1;
	$f=1;
	foreach($teams_teams as $teams_test){
		if($c==1 || ($c-1)%18==0){
			echo '<div class="gallery-frame" data-id="'.$f.'">';
			$f++;
		}
		if($teams_test->logo != '' && file_exists($teams_test->logo)) {
			list($width, $height) = @getimagesize($teams_test->logo);
			//192 155
			$image_path=$teams_test->logo;
			if($width>$height){
				//$max_width='width:192px;';
				$max_height='max-height:155px;';
				$bg_height='auto';
				$bg_width='100%';
			}else{
				$max_height='max-height:155px;';
				$max_width='max-width:192px;';
				$bg_width='auto';
				$bg_height='100%';
			}
		}else{
			$image_path='images/template/no-team.jpg';
		}
		$link=JRoute::_('index.php?option=com_teams&view=team&id='.$teams_test->id.':'.$teams_test->alias.'&Itemid=140');
		echo '	<div class="gallery-item-1 den">
						<a href="'.$link.'" class="fill" style="background-color:#FFF; background-size: '.@$bg_width.' '.@$bg_height.'; background-position: center center;'.@$max_width.@$max_height.';background-image:url(\''.$image_path.'\')" title="'.str_replace('"','\'',$teams_test->name).'"></a>
				</div>';
		if(($c>0 && $c%6==0) || $c==$count_teams_teams){
			echo '<div style="clear:both; height:0px; line-height:0px;"></div>';
		}
		if(($c>0 && $c%18==0) || $c==$count_teams_teams){
			echo '</div>';
		}
		$c++;
	}

?>
	</div>
</div>

<?php } else { ?>

<h3 class="module-title module-title--layout" id="supporters-results"><?php echo JText::_('TEAMS_SUPPORTERS');?></h3>
<div class="filters">
	<form action="<?php echo JURI::current(); ?><?=$anchor?>" class="form" method="get" name="search_teams_form" id="search_teams_form">
		<div class="filter-search">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_TEAMS_PLACEHOLDER'); ?>" name="search_supporters" id="search_supporters" value="<?php echo strip_tags(@$_REQUEST['search_supporters']);?>" />
			</div><!-- /input-group -->

		</div>
		<div class="filter-checkbox">
<?php
	$i=1;
	foreach($activities as $activity){
		echo '	<div class="form-group">
					<input id="box_s_'.$i.'" type="checkbox" name="activities_s[activity_s_'.$activity->id.']" '.(@$_REQUEST['activities_s']['activity_s_'.$activity->id]==$activity->id?'checked="checked"':'').' value="'.$activity->id.'" />
					<label for="box_s_'.$i.'" class="label-horizontal">'.($lang_code=='en'?$activity->name_en:$activity->name).'</label>
				 </div>';
		$i++;
	}
?>
		</div>
		<div class="block"></div>
		<button type="submit" style="padding: 5px 10px;" class="pull-right btn btn--coral btn--bold btn btn-primary validate" onclick="document.getElementById('search_teams_form').submit();"><?=($lang_code=='en'?'SEARCH':'ΑΝΑΖΗΤΗΣΗ')?></button>
		<button type="reset" onclick="clear_form(<?=$i?>,'box_s_','search_supporters');" style="padding: 5px 10px; margin-right:10px;" class="pull-right btn btn--grey btn--bold btn btn-primary validate"><?=($lang_code=='en'?'RESET':'ΚΑΘΑΡΙΣΜΟΣ')?></button>
		<div class="block" style="clear:both"></div>
	</form>
</div>
<div class="module module--synathina">

	<div class="gallery gallery--multirow gallery--filter" rel="js-start-gallery">
<?php
	if(count($teams_supporters)==0){
		if($lang_code=='en'){
			echo 'No results';
		}else{
			echo 'Δε βρέθηκαν αποτελέσματα αναζήτησης.';
		}
	}
	$c=1;
	$f=1;
	foreach($teams_supporters as $teams_test){
		if($c==1 || ($c-1)%18==0){
			echo '<div class="gallery-frame" data-id="'.$f.'">';
			$f++;
		}
		if($teams_test->logo!='' && file_exists($teams_test->logo)){
			list($width, $height) = @getimagesize($teams_test->logo);
			//192 155
			$image_path=$teams_test->logo;
			if($width>$height){
				//$max_width='width:192px;';
				$max_height='max-height:155px;';
				$bg_height='auto';
				$bg_width='100%';
			}else{
				$max_height='max-height:155px;';
				$max_width='max-width:192px;';
				$bg_width='auto';
				$bg_height='100%';
			}
		}else{
			$image_path='images/template/no-team.jpg';
		}
		$link=JRoute::_('index.php?option=com_teams&view=team&id='.$teams_test->id.':'.$teams_test->alias.'&Itemid=140');
		echo '<div class="gallery-item-1">
						<a href="'.$link.'" class="fill" style="background-color:#FFF; background-size: '.@$bg_width.' '.@$bg_height.'; background-position: center center;'.@$max_width.@$max_height.';background-image:url(\''.$image_path.'\')" title="'.str_replace('"','\'',$teams_test->name).'"></a>
					</div>';
		if(($c>0 && $c%6==0) || $c==$count_teams_supporters){
			echo '<div style="clear:both; height:0px; line-height:0px;"></div>';
		}
		if(($c>0 && $c%18==0) || $c==$count_teams_supporters){
			echo '</div>';
		}
		$c++;
	}

?>
	</div>
	<br />	<br />	<br />
</div>

<?php } ?>
