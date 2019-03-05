<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$config = JFactory::getConfig();

//local db
$db = JFactory::getDbo();

jimport('joomla.filesystem.folder');

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$query = "SELECT * FROM #__teams WHERE user_id='".$this->data->id."' LIMIT 1 ";
$db->setQuery($query);
$teams_data = $db->loadObjectList();
foreach($teams_data as $team_data){
	if($team_data->create_actions==1){
		$create_actions=1;
	}else{
		$create_actions=0;
	}
	if($team_data->support_actions==1){
		$support_actions=1;
	}else{
		$support_actions=0;
	}
	$team_id=$team_data->id;
	$anonymous=$team_data->hidden;
	$team_or_org=$team_data->team_or_org;
	$logo_path=$team_data->logo;
	$legal_form=$team_data->legal_form;
	$profit=$team_data->profit;
	$profit_id=$team_data->profit_id;
	$profit_custom=$team_data->profit_custom;
	$description=$team_data->description;
	$activities=array_filter(explode(',',$team_data->activities));
	$org_donation=array_filter(explode(',',$team_data->org_donation));
	$web_link=$team_data->web_link;
	$fb_link=$team_data->fb_link;
	$tw_link=$team_data->tw_link;
	$pn_link=$team_data->pn_link;
	$in_link=$team_data->in_link;
	$go_link=$team_data->go_link;
	$li_link=$team_data->li_link;
	$yt_link=$team_data->yt_link;
	$contact_1_name=$team_data->contact_1_name;
	$contact_1_email=$team_data->contact_1_email;
	$contact_1_phone=$team_data->contact_1_phone;
	$contact_2_name=$team_data->contact_2_name;
	$contact_2_email=$team_data->contact_2_email;
	$contact_2_phone=$team_data->contact_2_phone;
	$contact_3_name=$team_data->contact_3_name;
	$contact_3_email=$team_data->contact_3_email;
	$contact_3_phone=$team_data->contact_3_phone;
	$newsletter=$team_data->newsletter;
	$query = "SELECT * FROM #__team_photos WHERE team_id='".$team_id."' ORDER BY ordering ASC";
	$db->setQuery($query);
	$photos = $db->loadObjectList();
	$query = "SELECT path FROM #__team_files WHERE team_id='".$team_id."' ORDER BY ordering ASC LIMIT 1";
	$db->setQuery($query);
	$file_path = $db->loadResult();
}

?>

				<div class="form--bordered">
					<div class="l-fg6">
						 <div class="form-group">
								<label for="" class="is-block">EMAIL ΧΡΗΣΤΗ*</label>
								<?php echo $this->data->email; ?>
						 </div>
					</div>
				</div>
				<div class="form-inline form--bordered filters" rel="js-choose-action-type">
					<div class="form-group">
						<input id="box1" type="checkbox" name="jform[create_actions]" value="organizer" <?=($create_actions==1?'checked="checked"':'')?> disabled />
						<label  class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΔΙΟΡΓΑΝΩΣΩ ΔΡΑΣΕΙΣ</strong></label>
					</div>
					<div class="form-group">
						<input id="box2" type="checkbox" name="jform[support_actions]" value="supporter" <?=($support_actions==1?'checked="checked"':'')?> disabled >
						<label for="box2" class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΥΠΟΣΤΗΡΙΞΩ</strong> ΔΡΑΣΕΙΣ</label>
					</div>
<?php
	if($anonymous==1){
		echo '		<div class="hidden-team slider_index_row">
						<input id="hidden_team" type="checkbox" name="jform[hidden]" value="supporter" checked="checked" disabled >
						<label for="hidden_team" class="label-horizontal">ΑΝΩΝΥΜΟΣ ΥΠΟΣΤΗΡΙΚΤΗΣ</label>
					</div>';
	}
?>
				</div>
				<div class="form-inline form--bordered filters" >
				 <div class="form-group">
						 <input id="box3" type="radio" name="jform[team_or_org]" value="10" <?=($team_or_org==10?'checked="checked"':'')?> disabled>
						 <label for="box3" class="label-horizontal" >ΕΙΜΑΙ ΟΜΑΔΑ ΠΟΛΙΤΩΝ</label>
					</div>
					<div class="form-group">
						 <input id="box4" type="radio" name="jform[team_or_org]" value="11" <?=($team_or_org==11?'checked="checked"':'')?> disabled>
						 <label for="box4" class="label-horizontal" >ΕΙΜΑΙ ΦΟΡΕΑΣ/ΟΡΓΑΝΙΣΜΟΣ</label>
					</div>
					<div class="form-group">
						 <input id="box41" type="radio" name="jform[team_or_org]" value="12" <?=($team_or_org==12?'checked="checked"':'')?> disabled>
						 <label for="box41" class="label-horizontal">ΕΙΜΑΙ ΕΠΙΧΕΙΡΙΣΗ/ΕΤΑΙΡΕΙΑ</label>
					</div>
                    <div class="form-group">
						 <input id="box41" type="radio" name="jform[team_or_org]" value="13" <?=($team_or_org==13?'checked="checked"':'')?> disabled>
						 <label for="box41" class="label-horizontal">ΕΙΜΑΙ ΙΔΙΩΤΗΣ/ΔΗΜΟΤΗΣ</label>
					</div>
				</div>
				<div class="form-inline l-fg6">
					<div class="form-group">
						 <label for="box6" class="is-block">Όνομα*:</label>
						 <?php echo $this->data->name; ?>
					</div>
					<div class="form-group form-group--upload">
						 <label for="box7" class="is-block">Λογότυπο:</label>
<?php
	if ($logo_path!='') {
		echo '<img src="'.$logo_path.'" alt="" style="width:80px;" />';
	}
?>
					</div>
				</div>
				<div class="form-inline filters" style="margin-bottom:0px;" rel="js-choose-legal-type">
					<div class="form-group">
						<label for="leag_title" style="font-size: 16px;font-weight: bold;color: #5d5d5d;" class="is-block">Νομική μορφή*:</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters" style="padding-top: 0px;" rel="js-choose-legal-type">
				 <div class="form-group">
						 <input id="box8" type="radio" name="has_legal_type" value="yes" <?=($legal_form==1?'checked="checked"':'')?> disabled>
						 <label for="box8" class="label-horizontal">ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
					</div>
					<div class="form-group">
						 <input id="box9" type="radio" name="has_legal_type" value="no" <?=($legal_form==0?'checked="checked"':'')?> disabled>
						 <label for="box9" class="label-horizontal">ΔΕΝ ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters <?=($legal_form==0?'form-block--hidden':'')?>  js-has-types" rel="js-show-legal-types">
					<div class="form-group">
						 <input id="box10" type="radio" name="organization_type" value="yes" <?=($profit==0?'checked="checked"':'')?> disabled>
						 <label for="box10" class="label-horizontal">ΜΗ ΚΕΡΔΟΣΚΟΠΙΚΗ</label>
					</div>
					<div class="form-group">
						 <input id="box11" type="radio" name="organization_type" value="no" <?=($profit==1?'checked="checked"':'')?> disabled>
						 <label for="box11" class="label-horizontal">ΚΕΡΔΟΣΚΟΠΙΚΗ</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters <?=($profit==1?'form-block--hidden':'')?>" rel="js-show-profit-types">
<?php
	$query = " SELECT id, name "
			." FROM #__team_types WHERE published=1 "
			." ORDER BY id ASC ";

	$db->setQuery($query);
	$rows=$db->loadObjectList();
	$i=12;
	foreach($rows as $row){
		echo '<div class="form-group">
						<input id="box-type-'.$i.'" type="radio" name="profit_id" value="'.$row->id.'" '.($profit_id==$row->id?'checked="checked"':'').' rel="js-radio-profit" disabled>
						<label for="box-type-'.$i.'" class="label-horizontal">'.$row->name.'</label>
					</div>';
		$i++;
	}
?>
					<div class="form-group">
						 <label for="box150" class="is-inline-block">ΑΛΛΟ</label>
						 <input id="box150" type="text" name="profit_custom" rel="js-other-profit" value="<?=($profit_custom!=''?$profit_custom:'')?>" disabled>
					</div>
				</div>
				<div class="form-group form-inline is-block" style="padding-bottom:12px;">
					<label for="activity_description" class="is-block">Περιγραφή*:</label>
					<div style="display:block"><?php echo $description; ?></div>
				</div>
				<div class="form-inline filters registration-activities" style="padding-bottom:12px;">
					<label for="activity_description" class="is-block">Θεματική δραστηριοποίησης*:</label>
<?php
	$query = " SELECT id, name "
			." FROM #__team_activities
			WHERE published=1"
			." ORDER BY id ASC ";

	$db->setQuery($query);
	$rows=$db->loadObjectList();
	$i=1;
	foreach($rows as $row){
		echo '<div class="form-group">
						 <input id="box-activity-'.$i.'" type="checkbox" name="jform[activity_'.$row->id.']" disabled '.(in_array($row->id,$activities)?'checked="checked"':'').' >
						 <label for="box-activity-'.$i.'" class="label-horizontal">'.$row->name.'</label>
					</div>';
		$i++;
	}
?>
				</div>
				<div class="form-inline l-fg6 ">
					<div class="form-group">
						 <label for="" class="is-block">Ιστοσελίδα:</label>
						 <input type="text" name="jform[web_link]" value="<?php echo @$web_link; ?>" disabled />
					</div>
					<div class="form-group form-group--upload">
						 <label for="gallery_upload" class="is-block">Φωτογραφίες:</label>
<?php
	$directory = 'images/team_photos/'.$team_id.'/';
	$path = JPATH_SITE . '/' . $directory;
	$exclude = array('index.html');
	$images = JFolder::files($path, '.', null, null, $exclude );

	foreach($images as $image)
	{
			echo '<img style="width:80px; margin:0px 5px 5px 0px;" src="' . JUri::root() . $directory . '/' . $image . '" alt="" />';
	}

	foreach ($photos as $photo) {
			//echo '<img src="images/team_photos/'.$team_id.'/'.$photo->path.'" alt="" width="80" />&nbsp;';
	}

?>
						 <!--<span class="is-block is-italic">Ανεβάστε μία ή περισσότερες φωτογραφίες της ομάδας σας
				ή των δράσεων που έχετε διοργανώσει ή υποστηρίξει </span>-->
					</div>
				</div>
				<div class="form--bordered">
					<label for="activity_description" class="is-block">Social Media:</label>
					<div class="l-fg6">
						 <div class="form-group">
								<label for="" class="is-block">FACEBOOK</label>
								<input type="text" name="jform[fb_link]" value="<?php echo @$fb_link; ?>" disabled />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">INSTAGRAM</label>
								<input type="text" name="jform[in_link]" value="<?php echo @$in_link; ?>" disabled />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">YOUTUBE</label>
								<input type="text" name="jform[yt_link]" value="<?php echo @$yt_link; ?>" disabled />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">LINKEDIN</label>
								<input type="text" name="jform[li_link]" value="<?php echo @$li_link; ?>" disabled />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">TWITTER</label>
								<input type="text" name="jform[tw_link]" value="<?php echo @$tw_link; ?>" disabled />
						 </div>
					</div>
				</div>
				<div class="form--bordered">
					<label for="activity_description" class="is-block">Στοιχεία επικοινωνίας με το Συναθηνά</label>
					<div class="l-fg4">
						 <div class="form-group" data-row="1">
								<label for="contact_1_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ*:</label>
								<input type="text" name="jform[contact_1_name]" id="contact_1_name" value="<?php echo @$contact_1_name; ?>" required="" aria-required="true" disabled />
						 </div>
						 <div class="form-group" data-row="1">
								<label for="contact_1_email" class="is-block">EMAIL*:</label>
								<input type="email" name="jform[contact_1_email]" id="contact_1_email" value="<?php echo @$contact_1_email; ?>" required="" aria-required="true" disabled />
						 </div>
						 <div class="form-group" data-row="1">
								<label for="contact_1_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ*:</label>
								<input type="text" name="jform[contact_1_phone]" id="contact_1_phone" value="<?php echo @$contact_1_phone; ?>" required="" aria-required="true" disabled />
						 </div>
					</div>
					<div class="l-fg4">
						 <div class="form-group" data-row="2">
								<label for="contact_2_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
								<input type="text" name="jform[contact_2_name]" id="contact_2_name" value="<?php echo @$contact_2_name; ?>" disabled />
						 </div>
						 <div class="form-group" data-row="2">
								<label for="contact_2_email" class="is-block">EMAIL:</label>
								<input type="email" name="jform[contact_2_email]" id="contact_2_email" value="<?php echo @$contact_2_email; ?>" disabled />
						 </div>
						 <div class="form-group" data-row="2">
								<label for="contact_2_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
								<input type="text" name="jform[contact_2_phone]" id="contact_2_phone" value="<?php echo @$contact_2_phone; ?>" disabled />
						 </div>
					</div>
					<div class="l-fg4">
						 <div class="form-group" data-row="3">
								<label for="contact_3_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
								<input type="text" name="jform[contact_3_name]" id="contact_3_name" value="<?php echo @$contact_3_name; ?>" disabled />
						 </div>
						 <div class="form-group" data-row="3">
								<label for="contact_3_email" class="is-block">EMAIL:</label>
								<input type="email" name="jform[contact_3_email]" id="contact_3_email" value="<?php echo @$contact_3_email; ?>" disabled />
						 </div>
						 <div class="form-group" data-row="3">
								<label for="contact_3_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
								<input type="text" name="jform[contact_3_phone]" id="contact_3_phone" value="<?php echo @$contact_3_phone; ?>" disabled />
						 </div>
					</div>
				</div>
				<div class="form-inline l-fg6 form--bordered">
					<div class="form-group form-group--upload">
						 <label for="files_upload" class="is-block">ΠΑΡΟΥΣΙΑΣΗ ΟΜΑΔΑΣ:</label>
<?php
	if (file_exists($config->get( 'abs_path' ).'/images/team_files/'.$team_id.'/'.$file_path)) {
		echo $file_path;
	}
?>
						 <!--<span class="is-block is-italic"><small>(Ανεβάστε μία ή περισσότερες παρουσιάσεις της ομάδας σας σε μορφή *.doc ή *.pdf )</small></span>-->

					</div>
				</div>
				<div class="form-inline l-fg6 form--bordered">
					<label for="newsletter" class="is-block">Αποστολή ενημερωτικού δελτίου:</label>
					<div class="form-group">
						<input id="newsletter_yes" type="radio" name="jform[newsletter]" value="yes" <?=($newsletter==1?'checked="checked"':'')?> disabled>
						<label for="newsletter_yes" class="label-horizontal">NAI</label>
					</div>
					<div class="form-group">
						 <input id="newsletter_no" type="radio" name="jform[newsletter]" value="no" <?=($newsletter==0?'checked="checked"':'')?> disabled>
						 <label for="newsletter_no" class="label-horizontal">ΟΧΙ</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters  <?=($support_actions==1?'':'form-block--hidden')?> registration-donations" rel="js-show-action-type">
					<label for="activity_description" class="is-block">ΔΙΑΣΥΝΔΕΣΗ ΥΠΟΣΤΗΡΙΚΤΩΝ  ΜΕ ΔΙΟΡΓΑΝΩΤΕΣ</label>
<?php
	$query = " SELECT id, name "
			." FROM #__team_donation_types
				WHERE published=1 AND parent_id=0	"
			." ORDER BY id ASC ";

	$db->setQuery($query);
	$rows=$db->loadObjectList();
	$i=1;
	$children=array();
	foreach($rows as $row){
		$query = " SELECT id, name "
				." FROM #__team_donation_types
					WHERE published=1 AND parent_id=".$row->id."	"
				." ORDER BY id ASC ";

		$db->setQuery($query);
		$rows1=$db->loadObjectList();
		foreach($rows1 as $row1){
			$children[$i][]=array($row1->id, $row1->name,$row->id);
		}
		echo '<div class="form-group">
						 <input name="donation-'.$row->id.'" id="donation-'.$row->id.'" type="checkbox" value="show"  '.(@count($children[$i])>0?'data-href="#subcat'.$i.'"':'').' '.(in_array($row->id,$org_donation)?'checked="checked"':'').' disabled>
						 <label for="donation-'.$row->id.'" class="label-horizontal">'.$row->name.'</label>
					</div>';
		$i++;
	}

	for($i=1; $i<count($children)+1; $i++){
		for($y=0; $y<count($children[$i]); $y++){
			if($y==0){
				echo '<div id="subcat'.$i.'" rel="js-show-category-types" class="'.(in_array($children[$i][$y][2],$org_donation)?'':'form-block--hidden').' form-inline">';
			}
			echo '<div class="form-group">
							<input name="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" id="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'"  type="checkbox" '.(in_array($children[$i][$y][0],$org_donation)?'checked="checked"':'').' disabled>
							<label for="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" class="label-horizontal">'.$children[$i][$y][1].'</label>
						</div>';
			if($y==(count($children[$i])-1)){
				$other_donation='';
				if($children[$i][$y][2]==1){
					$other_donation=$team_data->donation_eidos;
				}
				if($children[$i][$y][2]==16){
					$other_donation=$team_data->donation_technology;
				}
				echo '<div class="form-group">
								<label for="donation-'.$children[$i][$y][2].'-other" class="label-horizontal" style="display:inline-block">'.($lang_code=='en'?'Other':'Άλλο').'</label>
								<input name="donation-'.$children[$i][$y][2].'-other" id="donation-'.$children[$i][$y][2].'-other"  type="text" disabled value="'.str_replace('"','\'',$other_donation).'">
							</div>';
				echo '</div>';
			}
		}

	}
?>
				</div>
				<div class="form-inline form--bordered filters">
					Αν θες να επεξεργαστείς τα στοιχεία σου κάνε κλικ <a style="vertical-align:top" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id);?>">εδώ</a>
				</div>