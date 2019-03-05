<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

// Load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$config = JFactory::getConfig();

//local db
$db = JFactory::getDbo();

// IMPORT EDITOR CLASS
jimport( 'joomla.html.editor' );

// GET EDITOR SELECTED IN GLOBAL SETTINGS
$global_editor = $config->get( 'editor' );

// GET USER'S DEFAULT EDITOR
$user_editor = JFactory::getUser()->getParam("editor");

if($user_editor && $user_editor !== 'JEditor') {
    $selected_editor = $user_editor;
} else {
    $selected_editor = $global_editor;
}

// INSTANTIATE THE EDITOR
$editor = JEditor::getInstance($selected_editor);

// SET EDITOR PARAMS
$params_editor = array( 'smilies'=> '0' ,
    'style'  => '1' ,
    'layer'  => '0' ,
    'table'  => '0' ,
    'clear_entities'=>'0'
);


$document  = JFactory::getDocument();
$renderer  = $document->loadRenderer('message');
$app = JFactory::getApplication();
//onclick="document.getElementById('email2').value=document.getElementById('email1').value; document.getElementById('password2').value=document.getElementById('password1').value;"
$renderer->render('message');

$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');

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
	$anonymous=$team_data->hidden;
	$team_id=$team_data->id;
	$team_or_org=$team_data->team_or_org;
	$logo_path=$team_data->logo;
	$legal_form=$team_data->legal_form;
	$profit=$team_data->profit;
	if($profit==1){
		$profit_id=0;
		$profit_custom='';
	}else{
		$profit_id=$team_data->profit_id;
		$profit_custom=$team_data->profit_custom;
	}
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

<script type="text/javascript">
function delete_confirmation(id,path,abspath) {
			var answer = confirm("Είστε σίγουροι πως θέλετε να διαγράψετέ αυτή την εικόνα;");
			if (answer){
				$.ajax({
					url: abspath+'delete_photo.php',
					type: 'post',
					data: {'path':path, 'id':id},
					success: function(data, status) {
						if(data == 1) {
							$('#photo-gallery-'+id).remove();
							alert('Η φωτογραφία διαγράφηκε επιτυχώς');
						}
					},
					error: function(xhr, desc, err) {
					}
				});
			}else{
				return false;
			}
 }
</script>

<div class="l-register show-profile">
<?php
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
?>
	<div class="module module--synathina">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register">
				<h3 class="popup-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h3>
<?php
	$str = preg_replace('/^\h*\v+/m', '', $this->document->getBuffer('message'));
	if(!empty($str)){
?>
				<div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php echo $this->document->getBuffer('message');?>
				</div>
<?php
	}
?>
			<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal well form" enctype="multipart/form-data">
				<div class="form--bordered">
					<div class="l-fg6">
						 <div class="form-group">
								<label for="" class="is-block">EMAIL ΧΡΗΣΤΗ*</label>
								<input type="email" name="jform[email1]" class="validate-email required" id="jform_email1" required="" value="<?php echo $this->data->email; ?>" aria-required="true">
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">ΕΠΑΝΑΛΗΨΗ EMAIL ΧΡΗΣΤΗ*</label>
								<input type="email" name="jform[email2]" class="validate-email required" id="jform_email2" required="" value="<?php echo $this->data->email; ?>" aria-required="true">
						 </div>
					</div>
					<div class="l-fg6">
						 <div class="form-group">
								<label for="" class="is-block">ΚΩΔΙΚΟΣ*</label>
								<input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password" maxlength="99" />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">ΕΠΑΝΑΛΗΨΗ ΚΩΔΙΚΟΥ*</label>
								<input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password" maxlength="99" />
						 </div>
					</div>
				</div>
				<div class="form-inline form--bordered filters" rel="js-choose-action-type">
					<div class="form-group">
						<input id="box1" type="checkbox" name="jform[create_actions]" value="organizer" <?=($create_actions==1?'checked="checked"':'')?> />
						<label for="box1" class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΔΙΟΡΓΑΝΩΣΩ ΔΡΑΣΕΙΣ</strong></label>
					</div>
					<div class="form-group">
						<input id="box2" type="checkbox" name="jform[support_actions]" value="supporter" <?=($support_actions==1?'checked="checked"':'')?> >
						<label for="box2" class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΥΠΟΣΤΗΡΙΞΩ</strong> ΔΡΑΣΕΙΣ</label>
					</div>
					<div class="hidden-team slider_index_row" <?=($anonymous==1?'':'style="display:none;"')?>>
						<input style="float:left;" id="hidden_team" type="checkbox" name="jform[hidden_team]" value="hidden_team" <?=($anonymous==1?'checked="checked"':'')?>  >
						<label style="float:left;" for="hidden_team" class="label-horizontal">ΑΝΩΝΥΜΟΣ ΥΠΟΣΤΗΡΙΚΤΗΣ</label>
						<a class="form-tooltip-jquery" href="#" title="<strong>Ανώνυμοι Υποστηρικτές</strong><br />Εάν επιθυμείτε να υποστηρίξετε τις δράσεις των ομάδων πολιτών που συμμετέχουν στο συνΑθηνά χωρίς να κοινοποιήσετε τα στοιχεία σας, μπορείτε να δημιουργήσετε ανώνυμο προφίλ χρήστη.<br />Κάθε φορά που υπάρχει ενδιαφέρον για την προσφορά σας, θα ενημερώνεστε με email για να συνδεθείτε με την εκάστοτε δράση.  Ενημερωτικά υπάρχει η δυνατότητα να αποκαλύψετε τα στοιχεία σας ανά πάσα στιγμή.">
							<i class="fa fa-question-circle" aria-hidden="true"></i>
						</a>
					</div>
				</div>
				<div class="form-inline form--bordered filters" >
				 <div class="form-group">
						 <input id="box3" type="radio" name="jform[team_or_org]" value="10" <?=($team_or_org==10?'checked="checked"':'')?>>
						 <label for="box3" class="label-horizontal" >ΕΙΜΑΙ ΟΜΑΔΑ ΠΟΛΙΤΩΝ</label>
					</div>
					<div class="form-group">
						 <input id="box4" type="radio" name="jform[team_or_org]" value="11" <?=($team_or_org==11?'checked="checked"':'')?>>
						 <label for="box4" class="label-horizontal" >ΕΙΜΑΙ ΦΟΡΕΑΣ/ΟΡΓΑΝΙΣΜΟΣ</label>
					</div>
					<div class="form-group">
						 <input id="box41" type="radio" name="jform[team_or_org]" value="12" <?=($team_or_org==12?'checked="checked"':'')?>>
						 <label for="box41" class="label-horizontal">ΕΙΜΑΙ ΕΠΙΧΕΙΡΙΣΗ/ΕΤΑΙΡΕΙΑ</label>
					</div>
					<div class="form-group">
						 <input id="box42" type="radio" name="jform[team_or_org]" value="13" <?=($team_or_org==13?'checked="checked"':'')?>>
						 <label for="box42" class="label-horizontal">ΕΙΜΑΙ ΙΔΙΩΤΗΣ/ΔΗΜΟΤΗΣ</label>
					</div>
				</div>
				<div class="form-inline l-fg6">
					<div class="form-group">
						 <label for="box6" class="is-block">Όνομα*:</label>
						 <input type="text" name="jform[name]" id="jform_name" value="<?php echo $this->data->name; ?>" class="required" required="" aria-required="true">
						 <small class="is-block is-italic small">(Ονομα ομάδας / Οργανισμού / Φορέα / Εταιρίας κτλ)</small>
					</div>
					<div class="form-group form-group--upload">
						 <label for="box7" class="is-block">Λογότυπο:</label>
					<input type="file" name="jform[logo]" id="box5" class="file-browser">
					<small class="is-block is-italic small">(Προτεινόμενη ανάλυση: 200x200 pixels - Μέγιστο επιτρεπόμενο όριο φωτογραφίας: 1MB)</small>
<?php
	if ($logo_path!='') {
		echo '<br /><img src="'.$logo_path.'" alt="" style="width:80px;" />';
	}
?>
					</div>
				</div>
				<div class="form-inline filters" style="margin-bottom:0px;" rel="js-choose-legal-type">
					<div class="form-group">
						<label for="leag_title" style="font-size: 16px;font-weight: bold;color: #5d5d5d;" class="is-block">Νομική μορφή*:</label>
						<small class="is-block is-italic small">Οι πληροφορίες που θα συμπληρώσετε εδώ χρησιμοποιούνται για στατιστικά στοιχεία του συνΑθηνά και δεν  θα είναι ορατές στο δημόσιο προφίλ σας.</small>
					</div>
				</div>
				<div class="form-inline form--bordered filters" style="padding-top: 0px;" rel="js-choose-legal-type">
				 <div class="form-group">
						 <input id="box8" type="radio" name="has_legal_type" value="yes" <?=($legal_form==1?'checked="checked"':'')?>>
						 <label for="box8" class="label-horizontal">ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
					</div>
					<div class="form-group">
						 <input id="box9" type="radio" name="has_legal_type" value="no" <?=($legal_form==0?'checked="checked"':'')?>>
						 <label for="box9" class="label-horizontal">ΔΕΝ ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters <?=($legal_form==0?'form-block--hidden':'')?>  js-has-types" rel="js-show-legal-types">
					<div class="form-group">
						 <input id="box10" type="radio" name="organization_type" value="yes" <?=($profit==0?'checked="checked"':'')?>>
						 <label for="box10" class="label-horizontal">ΜΗ ΚΕΡΔΟΣΚΟΠΙΚΗ</label>
					</div>
					<div class="form-group">
						 <input id="box11" type="radio" name="organization_type" value="no" <?=($profit==1?'checked="checked"':'')?>>
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
						<input id="box-type-'.$i.'" type="radio" name="profit_id" value="'.$row->id.'" '.($profit_id==$row->id?'checked="checked"':'').' rel="js-radio-profit">
						<label for="box-type-'.$i.'" class="label-horizontal">'.$row->name.'</label>
					</div>';
		$i++;
	}
?>
					<div class="form-group">
						 <label for="box150" class="is-inline-block">ΑΛΛΟ</label>
						 <input id="box150" type="text" name="profit_custom" rel="js-other-profit" value="<?=($profit_custom!=''?$profit_custom:'')?>">
					</div>
				</div>
				<div class="form-group form-inline is-block">
					<label for="activity_description" class="is-block">Περιγραφή*:</label>
					<!--<textarea class="form-control" id="activity_description" rows="8" required="" name="jform[description]"><?php echo $description; ?></textarea>-->
					<?php echo $editor->display('jform[description]', $description, '', '200', '20', '20', true, null, null, null, $params_editor); ?>
					<span class="is-block is-italic">(Μια βασική περιγραφή για την ομάδα & τις δράσεις σας)</span>
				</div>
				<div class="form-inline filters registration-activities">
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
						 <input id="box-activity-'.$i.'" type="checkbox" name="jform[activity_'.$row->id.']" '.(in_array($row->id,$activities)?'checked="checked"':'').' >
						 <label for="box-activity-'.$i.'" class="label-horizontal">'.$row->name.'</label>
					</div>';
		$i++;
	}
?>
				</div>
				<div class="form-inline l-fg6 ">
					<div class="form-group">
						 <label for="" class="is-block">Ιστοσελίδα:</label>
						 <input type="text" name="jform[web_link]" value="<?php echo @$web_link; ?>" />
					</div>
					<div class="form-group form-group--upload">
						 <label for="gallery_upload" class="is-block">Φωτογραφίες:</label>

<?php
	$directory = 'images/team_photos/'.$team_id.'/';
	$path = JPATH_SITE . '/' . $directory;
	$exclude = array('index.html');
	$images = JFolder::files($path, '.', null, null, $exclude );

	if(count($images)>0){
	echo '<div style="vertical-align:top; margin-bottom:10px;" class="clearfix">';
	foreach($images as $image)
	{
			//echo '<img style="width:80px; margin-right:5px;" src="' . JUri::root() . $directory . '/' . $image . '" alt="" />';
			//echo $image.'<br>';
	}
	foreach ($images as $image) {
		$image_array=explode('.',$image);
			echo '<div id="photo-gallery-'.$image_array[0].'" style="display:block; position:relative; float:left; vertical-align:top;width:80px; height:80px; margin:0px 2px 2px 0px;">
							<img style="position:absolute; top:0px; left:0px; width:80px; height:80px; vertical-align:top" src="images/team_photos/'.$team_id.'/'.$image.'" alt=""/>
							<a title="Διαγραφή" onclick="delete_confirmation(\''.$image_array[0].'\',\'images/team_photos/'.$team_id.'/'.$image.'\',\''.JUri::base().'\')" href="javascript:void(null)" style="position:absolute; right:5px; top 5px;color:red; font-size:16px; font-weight:bold;">X</a>
						</div>';
	}
	echo '</div>';
	}

?>

					<input type="file"  class="file-browser" id="gallery_upload" name="gallery_upload[]" multiple>
						 <span class="is-block is-italic">Ανεβάστε μία ή περισσότερες φωτογραφίες της ομάδας σας
				ή των δράσεων που έχετε διοργανώσει ή υποστηρίξει - Μέγιστο επιτρεπόμενο όριο φωτογραφίας: 1MB</span>
					</div>
				</div>
				<div class="form--bordered">
					<label for="activity_description" class="is-block">Social Media:</label>
					<div class="l-fg6">
						 <div class="form-group">
								<label for="" class="is-block">FACEBOOK</label>
								<input type="text" name="jform[fb_link]" value="<?php echo @$fb_link; ?>" />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">INSTAGRAM</label>
								<input type="text" name="jform[in_link]" value="<?php echo @$in_link; ?>" />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">YOUTUBE</label>
								<input type="text" name="jform[yt_link]" value="<?php echo @$yt_link; ?>" />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">LINKEDIN</label>
								<input type="text" name="jform[li_link]" value="<?php echo @$li_link; ?>" />
						 </div>
						 <div class="form-group">
								<label for="" class="is-block">TWITTER</label>
								<input type="text" name="jform[tw_link]" value="<?php echo @$tw_link; ?>" />
						 </div>
					</div>
				</div>
				<div class="form--bordered">
					<label for="activity_description" class="is-block">Στοιχεία επικοινωνίας με το Συναθηνά</label>
					<span class="is-block is-italic">(Τα στοιχεία που καταχωρείτε στα συγκεκριμένα πεδία χρησιμοποιούνται αποκλειστικά για τους σκοπούς επικοινωνίας με την ομάδα του συνΑθηνά και δεν θα είναι ορατά στο δημόσιο προφίλ σας.)</span><br />
					<div class="l-fg4">
						 <div class="form-group" data-row="1">
								<label for="contact_1_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ*:</label>
								<input type="text" name="jform[contact_1_name]" id="contact_1_name" value="<?php echo @$contact_1_name; ?>" required="" aria-required="true" />
						 </div>
						 <div class="form-group" data-row="1">
								<label for="contact_1_email" class="is-block">EMAIL*:</label>
								<input type="email" name="jform[contact_1_email]" id="contact_1_email" value="<?php echo @$contact_1_email; ?>" required="" aria-required="true" />
						 </div>
						 <div class="form-group" data-row="1">
								<label for="contact_1_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ*:</label>
								<input type="text" name="jform[contact_1_phone]" id="contact_1_phone" value="<?php echo @$contact_1_phone; ?>" required="" aria-required="true"  />
						 </div>
					</div>
					<div class="l-fg4">
						 <div class="form-group" data-row="2">
								<label for="contact_2_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
								<input type="text" name="jform[contact_2_name]" id="contact_2_name" value="<?php echo @$contact_2_name; ?>"  />
						 </div>
						 <div class="form-group" data-row="2">
								<label for="contact_2_email" class="is-block">EMAIL:</label>
								<input type="email" name="jform[contact_2_email]" id="contact_2_email" value="<?php echo @$contact_2_email; ?>"  />
						 </div>
						 <div class="form-group" data-row="2">
								<label for="contact_2_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
								<input type="text" name="jform[contact_2_phone]" id="contact_2_phone" value="<?php echo @$contact_2_phone; ?>"  />
						 </div>
					</div>
					<div class="l-fg4">
						 <div class="form-group" data-row="3">
								<label for="contact_3_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
								<input type="text" name="jform[contact_3_name]" id="contact_3_name" value="<?php echo @$contact_3_name; ?>"  />
						 </div>
						 <div class="form-group" data-row="3">
								<label for="contact_3_email" class="is-block">EMAIL:</label>
								<input type="email" name="jform[contact_3_email]" id="contact_3_email" value="<?php echo @$contact_3_email; ?>"  />
						 </div>
						 <div class="form-group" data-row="3">
								<label for="contact_3_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
								<input type="text" name="jform[contact_3_phone]" id="contact_3_phone" value="<?php echo @$contact_3_phone; ?>"  />
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
						<input type="file" name="jform[files_upload]" id="files_upload" class="file-browser">
						<span class="is-block is-italic">(Εδώ μπορείτε να ανεβάσετε αρχεία με αναλυτικότερες παρουσιάσεις, ισολογισμούς και οποιοδήποτε άλλο στοιχείο θεωρείτε ότι επεξηγεί καλύτερα τις δράσεις σας - Μέγιστο επιτρεπόμενο όριο αρχείου: 1MB)</span>

					</div>
				</div>
				<div class="form-inline l-fg6 form--bordered">
					<label for="newsletter" class="is-block">Αποστολή ενημερωτικού δελτίου:</label>
					<div class="form-group">
						<input id="newsletter_yes" type="radio" name="jform[newsletter]" value="yes" <?=($newsletter==1?'checked="checked"':'')?> >
						<label for="newsletter_yes" class="label-horizontal">NAI</label>
					</div>
					<div class="form-group">
						 <input id="newsletter_no" type="radio" name="jform[newsletter]" value="no" <?=($newsletter==0?'checked="checked"':'')?> >
						 <label for="newsletter_no" class="label-horizontal">ΟΧΙ</label>
					</div>
				</div>
				<div class="form-inline form--bordered filters <?=($support_actions==1?'':'form-block--hidden')?> registration-donations" rel="js-show-action-type">
					<label for="activity_description" class="is-block">ΤΙ ΠΡΟΣΦΕΡΩ ΩΣ ΥΠΟΣΤΗΡΙΚΤΗΣ ΔΡΑΣΕΩΝ</label>
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
						 <input class="donation-change registration-donations-parent" name="donation-'.$row->id.'" id="donation-'.$row->id.'" type="checkbox" value="show"  '.(@count($children[$i])>0?'data-href="#subcat'.$i.'"':'').' '.(in_array($row->id,$org_donation)?'checked="checked"':'').'>
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
							<input name="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" id="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'"  type="checkbox" '.(in_array($children[$i][$y][0],$org_donation)?'checked="checked"':'').'>
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
								<input name="donation-'.$children[$i][$y][2].'-other" id="donation-'.$children[$i][$y][2].'-other"  type="text" value="'.str_replace('"','\'',$other_donation).'">
							</div>';
				echo '</div>';
			}
		}

	}
?>
				</div>
				<div class="form-group form-group--tail is-block clearfix">
					<span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
					<button type="submit" class="pull-right btn btn--coral btn--bold btn btn-primary validate">Ανανέωση</button>
					<a class="pull-right btn btn--coral btn--bold btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>" title="<?php echo JText::_('JCANCEL'); ?>" style="color:#FFF; padding: 5px 10px; margin-right:5px;background: #fc8f0a;"><?php echo JText::_('JCANCEL'); ?></a>
				</div>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" id="team_id" name="team_id" value="<?php echo $team_id; ?>" />
				<input type="hidden" name="task" value="profile.save" />
				<input type="hidden" name="jform[username]" id="jform_username" value="com_users" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
			</div>
		</div>
	</div>
</div>