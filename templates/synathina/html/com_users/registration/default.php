<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//JHtml::_('behavior.keepalive');
//JHtml::_('behavior.formvalidator');

$db = JFactory::getDbo();
$config = JFactory::getConfig();

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$document  = JFactory::getDocument();
$renderer  = $document->loadRenderer('message');
$app = JFactory::getApplication();
//onclick="document.getElementById('email2').value=document.getElementById('email1').value; document.getElementById('password2').value=document.getElementById('password1').value;"
$renderer->render('message');

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

?>


<div class="l-register">
	<div class="module module--synathina">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register">
				<h3 class="popup-title">Εγγραφή</h3>
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
				<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal well form" enctype="multipart/form-data">
					<div class="form--bordered">
						<!--<div class="l-fg6">
							 <div class="form-group">
									<label for="" class="is-block">ΟΝΟΜΑ ΧΡΗΣΤΗ*</label>
									<input type="text" name="jform[username]" id="jform_username" value="<?php //echo @$_REQUEST['jform[username]']; ?>" class="validate-username required" required="" aria-required="true">
							 </div>
						</div>-->
						<div class="l-fg6">
							 <div class="form-group">
									<label for="" class="is-block">EMAIL ΧΡΗΣΤΗ*</label>
									<input type="email" name="jform[email1]" class="validate-email required" id="jform_email1" value="<?php echo @$_REQUEST['jform[email1]']; ?>" required="" aria-required="true">
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">ΕΠΑΝΑΛΗΨΗ EMAIL ΧΡΗΣΤΗ*</label>
									<input type="email" name="jform[email2]" class="validate-email required" id="jform_email2" value="<?php echo @$_REQUEST['jform[email2]']; ?>" required="" aria-required="true">
							 </div>
						</div>
						<div class="l-fg6">
							 <div class="form-group">
									<label for="" class="is-block">ΚΩΔΙΚΟΣ*</label>
									<input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password required" maxlength="99" required="" aria-required="true">
									<small class="is-block is-italic small">(Παρακαλώ εισάγετε έναν κωδικό με τουλάχιστον 4 χαρακτήρες)</small>
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">ΕΠΑΝΑΛΗΨΗ ΚΩΔΙΚΟΥ*</label>
									<input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password required" maxlength="99" required="" aria-required="true">
							 </div>
						</div>
					</div>
					<div class="form-inline form--bordered filters" rel="js-choose-action-type">
						<div class="form-group">
							<input id="box1" type="checkbox" name="jform[create_actions]" value="organizer" />
							<label for="box1" class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΔΙΟΡΓΑΝΩΣΩ ΔΡΑΣΕΙΣ</strong></label>
						</div>
						<div class="form-group">
							<input id="box2" type="checkbox" name="jform[support_actions]" value="supporter" >
							<label for="box2" class="label-horizontal">ΘΕΛΩ ΝΑ <strong>ΥΠΟΣΤΗΡΙΞΩ</strong> ΔΡΑΣΕΙΣ</label>
						</div>
						<div class="hidden-team slider_index_row" style="display:none;">
							<input style="float:left;" id="hidden_team" type="checkbox" name="jform[hidden_team]" value="hidden_team" >
							<label style="float:left;" for="hidden_team" class="label-horizontal">ΑΝΩΝΥΜΟΣ ΥΠΟΣΤΗΡΙΚΤΗΣ</label>
							<a class="form-tooltip-jquery" href="#" title="<strong>Ανώνυμοι Υποστηρικτές</strong><br />Εάν επιθυμείτε να υποστηρίξετε τις δράσεις των ομάδων πολιτών που συμμετέχουν στο συνΑθηνά χωρίς να κοινοποιήσετε τα στοιχεία σας, μπορείτε να δημιουργήσετε ανώνυμο προφίλ χρήστη.<br />Κάθε φορά που υπάρχει ενδιαφέρον για την προσφορά σας, θα ενημερώνεστε με email για να συνδεθείτε με την εκάστοτε δράση.  Ενημερωτικά υπάρχει η δυνατότητα να αποκαλύψετε τα στοιχεία σας ανά πάσα στιγμή.">
								<i class="fa fa-question-circle" aria-hidden="true"></i>
							</a>
						</div>
					</div>
					<div class="form-inline form--bordered filters" >
					 <div class="form-group">
							 <input id="box3" type="radio" name="jform[team_or_org]" value="10">
							 <label for="box3" class="label-horizontal" >ΕΙΜΑΙ ΟΜΑΔΑ ΠΟΛΙΤΩΝ</label>
						</div>
						<div class="form-group">
							 <input id="box4" type="radio" name="jform[team_or_org]" value="11">
							 <label for="box4" class="label-horizontal" >ΕΙΜΑΙ ΦΟΡΕΑΣ/ΟΡΓΑΝΙΣΜΟΣ</label>
						</div>
						<div class="form-group">
							 <input id="box41" type="radio" name="jform[team_or_org]" value="12">
							 <label for="box41" class="label-horizontal">ΕΙΜΑΙ ΕΠΙΧΕΙΡΙΣΗ/ΕΤΑΙΡΕΙΑ</label>
						</div>
                        <div class="form-group">
							 <input id="box42" type="radio" name="jform[team_or_org]" value="13">
							 <label for="box42" class="label-horizontal">ΕΙΜΑΙ ΙΔΙΩΤΗΣ/ΔΗΜΟΤΗΣ</label>
						</div>
					</div>
					<div class="form-inline l-fg6">
						<div class="form-group">
							 <label for="box6" class="is-block">Όνομα*:</label>
							 <input type="text" name="jform[name]" id="jform_name" value="" class="required" required="" aria-required="true">
							 <small class="is-block is-italic small">(Ονομα ομάδας / Οργανισμού / Φορέα / Εταιρίας κτλ)</small>
						</div>
						<div class="form-group form-group--upload">
							 <label for="box7" class="is-block">Λογότυπο:</label>
							 <input type="file" name="jform[logo]" id="box5" class="file-browser">
							 <small class="is-block is-italic small">(Προτεινόμενη ανάλυση: 200x200 pixels - Μέγιστο επιτρεπόμενο όριο φωτογραφίας: 1MB)</small>
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
							 <input id="box8" type="radio" name="has_legal_type" value="yes">
							 <label for="box8" class="label-horizontal">ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
						</div>
						<div class="form-group">
							 <input id="box9" type="radio" name="has_legal_type" value="no">
							 <label for="box9" class="label-horizontal">ΔΕΝ ΕΧΩ ΝΟΜΙΚΗ ΜΟΡΦΗ</label>
						</div>
					</div>
					<div class="form-inline form--bordered filters form-block--hidden js-has-types" rel="js-show-legal-types">
						<div class="form-group">
							 <input id="box10" type="radio" name="organization_type" value="yes" disabled>
							 <label for="box10" class="label-horizontal">ΜΗ ΚΕΡΔΟΣΚΟΠΙΚΗ</label>
						</div>
						<div class="form-group">
							 <input id="box11" type="radio" name="organization_type" value="no" disabled>
							 <label for="box11" class="label-horizontal">ΚΕΡΔΟΣΚΟΠΙΚΗ</label>
						</div>
					</div>
					<div class="form-inline form--bordered filters form-block--hidden" rel="js-show-profit-types">
<?php
		$query = " SELECT id, name "
				." FROM #__team_types WHERE published=1 "
				." ORDER BY id ASC ";

		$db->setQuery($query);
		$rows=$db->loadObjectList();
		$i=12;
		foreach($rows as $row){
			echo '<div class="form-group">
							<input id="box-type-'.$i.'" type="radio" name="profit_id" value="'.$row->id.'" rel="js-radio-profit" disabled>
							<label for="box-type-'.$i.'" class="label-horizontal">'.$row->name.'</label>
						</div>';
			$i++;
		}
?>
						<div class="form-group">
							 <label for="box150" class="is-inline-block">ΑΛΛΟ</label>
							 <input id="box150" type="text" name="profit_custom" rel="js-other-profit" disabled>
						</div>
					</div>
					<div class="form-group form-inline is-block">
						<label for="activity_description" class="is-block">Περιγραφή*:</label>
						<?php echo $editor->display('jform[description]', @$_REQUEST['jform[description]'], '', '200', '20', '20', true, null, null, null, $params_editor); ?>
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
							 <input id="box-activity-'.$i.'" type="checkbox" name="jform[activity_'.$row->id.']" >
							 <label for="box-activity-'.$i.'" class="label-horizontal">'.$row->name.'</label>
						</div>';
			$i++;
		}
?>
					</div>
					<div class="form-inline l-fg6 ">
						<div class="form-group">
							 <label for="" class="is-block">Ιστοσελίδα:</label>
							 <input type="text" name="jform[web_link]" value="<?php echo @$_REQUEST['jform[web_link]']; ?>" />
						</div>
						<div class="form-group form-group--upload">
							 <label for="gallery_upload" class="is-block">Φωτογραφίες:</label>
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
									<input type="text" name="jform[fb_link]" value="<?php echo @$_REQUEST['jform[fb_link]']; ?>" />
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">INSTAGRAM</label>
									<input type="text" name="jform[in_link]" value="<?php echo @$_REQUEST['jform[in_link]']; ?>" />
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">YOUTUBE</label>
									<input type="text" name="jform[yt_link]" value="<?php echo @$_REQUEST['jform[yt_link]']; ?>" />
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">LINKEDIN</label>
									<input type="text" name="jform[li_link]" value="<?php echo @$_REQUEST['jform[li_link]']; ?>" />
							 </div>
							 <div class="form-group">
									<label for="" class="is-block">TWITTER</label>
									<input type="text" name="jform[tw_link]" value="<?php echo @$_REQUEST['jform[tw_link]']; ?>" />
							 </div>
						</div>
					</div>
					<div class="form--bordered">
						<label for="activity_description" class="is-block">Στοιχεία επικοινωνίας με το Συναθηνά</label>
						<span class="is-block is-italic">(Τα στοιχεία που καταχωρίζετε στα συγκεκριμένα πεδία χρησιμοποιούνται αποκλειστικά για τους σκοπούς επικοινωνίας με την ομάδα του συνΑθηνά και δεν θα είναι ορατά στο δημόσιο προφίλ σας.)</span><br />
						<div class="l-fg4">
							 <div class="form-group" data-row="1">
									<label for="contact_1_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ*:</label>
									<input type="text" name="jform[contact_1_name]" id="contact_1_name" value="<?php echo @$_REQUEST['jform[contact_1_name]']; ?>" required="" aria-required="true" />
							 </div>
							 <div class="form-group" data-row="1">
									<label for="contact_1_email" class="is-block">EMAIL*:</label>
									<input type="email" name="jform[contact_1_email]" id="contact_1_email" value="<?php echo @$_REQUEST['jform[contact_1_email]']; ?>" required="" aria-required="true" />
							 </div>
							 <div class="form-group" data-row="1">
									<label for="contact_1_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ*:</label>
									<input type="text" name="jform[contact_1_phone]" id="contact_1_phone" value="<?php echo @$_REQUEST['jform[contact_1_phone]']; ?>" required="" aria-required="true" />
							 </div>
						</div>
						<div class="l-fg4">
							 <div class="form-group" data-row="2">
									<label for="contact_2_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
									<input type="text" name="jform[contact_2_name]" id="contact_2_name" value="<?php echo @$_REQUEST['jform[contact_2_name]']; ?>" />
							 </div>
							 <div class="form-group" data-row="2">
									<label for="contact_2_email" class="is-block">EMAIL:</label>
									<input type="email" name="jform[contact_2_email]" id="contact_2_email" value="<?php echo @$_REQUEST['jform[contact_2_email]']; ?>" />
							 </div>
							 <div class="form-group" data-row="2">
									<label for="contact_2_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
									<input type="text" name="jform[contact_2_phone]" id="contact_2_phone" value="<?php echo @$_REQUEST['jform[contact_2_phone]']; ?>" />
							 </div>
						</div>
						<div class="l-fg4">
							 <div class="form-group" data-row="3">
									<label for="contact_3_name" class="is-block">ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ:</label>
									<input type="text" name="jform[contact_3_name]" id="contact_3_name" value="<?php echo @$_REQUEST['jform[contact_3_name]']; ?>" />
							 </div>
							 <div class="form-group" data-row="3">
									<label for="contact_3_email" class="is-block">EMAIL:</label>
									<input type="email" name="jform[contact_3_email]" id="contact_3_email" value="<?php echo @$_REQUEST['jform[contact_3_email]']; ?>" />
							 </div>
							 <div class="form-group" data-row="3">
									<label for="contact_3_phone" class="is-block">ΚΙΝΗΤΟ ΤΗΛΕΦΩΝΟ:</label>
									<input type="text" name="jform[contact_3_phone]" id="contact_3_phone" value="<?php echo @$_REQUEST['jform[contact_3_phone]']; ?>" />
							 </div>
						</div>
					</div>
					<div class="form-inline l-fg6 form--bordered">
						<div class="form-group form-group--upload">
							 <label for="files_upload" class="is-block">ΠΑΡΟΥΣΙΑΣΗ ΟΜΑΔΑΣ:</label>
							 <input type="file" name="jform[files_upload]" id="files_upload" class="file-browser">
							 <span class="is-block is-italic">(Εδώ μπορείτε να ανεβάσετε αρχεία με αναλυτικότερες παρουσιάσεις, ισολογισμούς και οποιοδήποτε άλλο στοιχείο θεωρείτε ότι επεξηγεί καλύτερα τις δράσεις σας - Μέγιστο επιτρεπόμενο όριο αρχείου: 1MB)</span>

						</div>
					</div>
					<div class="form-inline l-fg6 form--bordered">
						<label for="newsletter" class="is-block">Αποστολή ενημερωτικού δελτίου:</label>
						<div class="form-group">
							<input id="newsletter_yes" type="radio" name="jform[newsletter]" value="yes" checked="checked">
							<label for="newsletter_yes" class="label-horizontal">NAI</label>
						</div>
						<div class="form-group">
							 <input id="newsletter_no" type="radio" name="jform[newsletter]" value="no">
							 <label for="newsletter_no" class="label-horizontal">ΟΧΙ</label>
						</div>
					</div>
					<div class="form-inline form--bordered filters form-block--hidden registration-donations" rel="js-show-action-type">
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
							 <input class="registration-donations-parent" name="donation-'.$row->id.'" id="donation-'.$row->id.'" type="checkbox" value="show"  '.(@count($children[$i])>0?'data-href="#subcat'.$i.'"':'').' disabled>
							 <label for="donation-'.$row->id.'" class="label-horizontal">'.$row->name.'</label>
						</div>';
			$i++;
		}

		for($i=1; $i<count($children)+1; $i++){
			for($y=0; $y<count($children[$i]); $y++){
				if($y==0){
					echo '<div id="subcat'.$i.'" rel="js-show-category-types" class="form-block--hidden form-inline">';
				}
				echo '<div class="form-group">
								<input name="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" id="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'"  type="checkbox" disabled>
								<label for="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" class="label-horizontal">'.$children[$i][$y][1].'</label>
							</div>';
				if($y==(count($children[$i])-1)){
					echo '<div class="form-group">
									<label for="donation-'.$children[$i][$y][2].'-other" class="label-horizontal" style="display:inline-block">'.($lang_code=='en'?'Other':'Άλλο').'</label>
									<input name="donation-'.$children[$i][$y][2].'-other" id="donation-'.$children[$i][$y][2].'-other"  type="text">
								</div>';
					echo '</div>';
				}
			}

		}
?>
					</div>
					<div class="form-inline l-fg6 form--bordered">
						<div class="form-group">
							<input id="terms" type="checkbox" name="terms" value="terms" required />
							<label for="terms" class="is-block">* Αποδοχή <a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=6&Itemid=119');?>" target="_blank">όρων χρήσης</a></label>
						</div>
					</div>

					<div class="form-group form-group--tail is-block clearfix">
						<span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
						<button type="submit" class="pull-right btn btn--coral btn--bold btn btn-primary validate">Καταχώριση</button>
					</div>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="jform[username]" id="jform_username" value="com_users" />
					<input type="hidden" name="task" value="registration.register" />
					<?php echo JHtml::_('form.token');?>

<!--
<fieldset>
	<legend>Εγγραφή Χρήστη</legend>
	<div class="control-group">
		<div class="control-label">
			<span class="spacer">
				<span class="before"/>
				<span class="text">
					<label id="jform_spacer-lbl" class="">
						<strong class="red">*</strong> Υποχρεωτικό πεδίο</label>
				</span>
				<span class="after"/>
			</span>
		</div>
		<div class="controls">
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label id="jform_name-lbl" for="jform_name" class="hasTooltip required" title="<strong>Όνομα</strong>
					<br />Εισάγετε το πλήρες ονομά σας">
	Όνομα:<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="controls">
				<input type="text" name="jform[name]" id="jform_name" value="" class="required" size="30" required="" aria-required="true">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label id="jform_username-lbl" for="jform_username" class="hasTooltip required" title="<strong>Όνομα Χρήστη</strong>
							<br />Εισάγετε το επιθυμητό όνομα χρήστη.">
	Όνομα Χρήστη:<span class="star">&nbsp;*</span>
						</label>
					</div>
					<div class="controls">
						<input type="text" name="jform[username]" id="jform_username" value="" class="validate-username required" size="30" required="" aria-required="true">
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_password1-lbl" for="jform_password1" class="hasTooltip required" title="<strong>Κωδικός</strong>
									<br />Εισάγετε τον επιθυμητό σας κώδικό.">
	Κωδικός:<span class="star">&nbsp;*</span>
								</label>
							</div>
							<div class="controls">
								<input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password required" size="30" maxlength="99" required="" aria-required="true">
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label id="jform_password2-lbl" for="jform_password2" class="hasTooltip required" title="<strong>Επιβεβαίωση Κωδικού</strong>
											<br />Επιβεβαιώστε τον κωδικό σας">
	Επιβεβαίωση Κωδικού:<span class="star">&nbsp;*</span>
										</label>
									</div>
									<div class="controls">
										<input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password required" size="30" maxlength="99" required="" aria-required="true">
										</div>
									</div>
									<div class="control-group">
										<div class="control-label">
											<label id="jform_email1-lbl" for="jform_email1" class="hasTooltip required" title="<strong>Διεύθυνση Ηλεκτρονικού Ταχυδρομείου</strong>
													<br />Εισάγετε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας">
	Διεύθυνση Ηλεκτρονικού Ταχυδρομείου:<span class="star">&nbsp;*</span>
												</label>
											</div>
											<div class="controls">
												<input type="email" name="jform[email1]" class="validate-email required" id="jform_email1" value="" size="30" autocomplete="email" required="" aria-required="true">
												</div>
											</div>
											<div class="control-group">
												<div class="control-label">
													<label id="jform_email2-lbl" for="jform_email2" class="hasTooltip required" title="<strong>Επιβεβαίωση Διεύθυνσης Ηλεκτρονικού Ταχυδρομείου</strong>
															<br />Επιβεβαιώστε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας">
	Επιβεβαίωση Διεύθυνσης Ηλεκτρονικού Ταχυδρομείου:<span class="star">&nbsp;*</span>
														</label>
													</div>
													<div class="controls">
														<input type="email" name="jform[email2]" class="validate-email required" id="jform_email2" value="" size="30" required="" aria-required="true">
														</div>
													</div>
												</fieldset>

					<?php // Iterate through the form fieldsets and display each one. ?>
					<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
						<?php $fields = $this->form->getFieldset($fieldset->name);?>
						<?php if (count($fields)):?>
							<fieldset>
							<?php // If the fieldset has a label set, display it as the legend. ?>
							<?php if (isset($fieldset->label)): ?>
								<legend><?php echo JText::_($fieldset->label);?></legend>
							<?php endif;?>
							<?php // Iterate through the fields in the set and display them. ?>
							<?php foreach ($fields as $field) : ?>
								<?php // If the field is hidden, just display the input. ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input;?>
								<?php else:?>
									<div class="control-group">
										<div class="control-label">
										<?php echo $field->label; ?>
										<?php if (!$field->required && $field->type != 'Spacer') : ?>
											<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
										<?php endif; ?>
										</div>
										<div class="controls">
											<?php echo $field->input;?>
										</div>
									</div>
								<?php endif;?>
							<?php endforeach;?>
							</fieldset>
						<?php endif;?>
					<?php endforeach;?>
					<div class="control-group">
						<div class="controls">
							<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JREGISTER');?></button>
							<a class="btn" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
							<input type="hidden" name="jform[option" value="com_users" />
							<input type="hidden" name="jform[task" value="registration.register" />
						</div>
					</div>
					<?php echo JHtml::_('form.token');?>-->
				</form>
			</div>
		</div>
	</div>
</div>

