<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//session in form to prevent double submits
$session = JFactory::getSession();
$user = JFactory::getUser();
$session->set( 'newform', md5($user->id.'-'.time()) );
$newform_session=$session->get( 'newform' );

//db connection
$db = JFactory::getDBO();

$config = JFactory::getConfig();

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

//get team
$initial_team = $this->team;

if (isset($initial_team->published) && $initial_team->published != 1 && $isroot != 1) {
	echo '<div class="l-register">
				 <div class="module module--synathina">
						<div class="module-skewed">
							<div class="register">
								<h3 class="popup-title">Καταχώριση δράσης</h3>
								<p>Ο λογαριασμός σας δεν έχει ενεργοποιηθεί ακόμα από το συνΑθηνά.</p>
								<br /><br />
							</div>
						</div>
					</div>
				</div>';
} else {

$months = array(1=>'ΙΑΝ','ΦΕΒ','ΜΑΡ','ΑΠΡ','ΜΑΙ','ΙΟΥΝ','ΙΟΥΛ','ΑΥΓ','ΣΕΠ','ΟΚΤ','ΝΟΕ','ΔΕΚ');


$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

//get menu clues
//$menu_params = $app->getMenu()->getActive()->params;
//$menu_link = $app->getMenu()->getActive()->link;

//$action=$this->action[0];
//$subactions=$this->subactions;

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

$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');
?>
<script>
function openChild(file,window,id,lat,lng) {
	file=file+'?address_name='+id+'&lng_name='+lng+'&lat_name='+lat+'&lat='+document.getElementById(lat).value+'&lng='+document.getElementById(lng).value+'&address='+document.getElementById(id).value;
  var left = (screen.width/2)-(750/2);
  var top = (screen.height/2)-(550/2);
	childWindow=open(file,window,'resizable=no,width=750,height=550, top='+top+', left='+left);
	if (childWindow.opener == null) childWindow.opener = self;
}
function show_hide(f,show){
	var f1=parseInt(f)+1;
	var f2=parseInt(f)-1;
	if(show==1){
		$('#form-block-'+f1).fadeToggle("slow", "linear");
		$('#ypotitlos_drashs_'+f1).val($('#ypotitlos_drashs_'+f).val());
		$('#address_'+f1).val($('#address_'+f).val());
		$('#lat_'+f1).val($('#lat_'+f).val());
		$('#lng_'+f1).val($('#lng_'+f).val());
		$('.add_button').css('display','none');
		$('#add_'+f1).css('display','block');
		$('#remove_'+f1).attr('style','visibility:visible!important');
	}else{
		$('#ypotitlos_drashs_'+f).attr('value','');
		$('#form-block-'+f).fadeToggle("slow", "linear");
		$('#address_'+f).val();
		$('#lat_'+f).val();
		$('#lng_'+f).val();
		$('#add_'+f).css('display','block');
		if(f>1){
			$('#remove_'+f2).attr('style','visibility:visible!important');
		}
		$('#add_'+f2).css('display','block');
	}
}

</script>

<div class="l-register">
<?php
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
?>
   <div class="module module--synathina">
      <div class="module-skewed">
         <!-- Content Module -->
         <div class="register">
            <h3 class="popup-title">Καταχώριση δράσης</h3>
						<span>
							Για να καταχωρίσετε τη δράση σας είναι απαραίτητο να συμπληρώσετε τα στοιχεία τουλάχιστον μίας επιμέρους δράσης.
							<br />Σε περίπτωση που η πρωτοβουλία σας σας είναι επαναλαμβανόμενη ή περιλαμβάνει επιπλέον δράσεις σε διαφορετικό τόπο ή χρόνο, μπορείτε να πιέσετε το + για να προσθέσετε τα απαραίτητα στοιχεία.
							<br />Παρακαλούμε βεβαιωθείτε ότι έχετε καταχωρίσει όλα τα σημεία στο χάρτη καθώς και τις διαφορετικές ημέρες και ώρες.<br /><br />
						</span>
            <form action="<?php echo JURI::current();?>" class="form" method="post" name="create_action" id="create_action" enctype="multipart/form-data">
<?php
	if($isroot){
?>
							<div class="form-inline l-fg6 max-600">
								<div class="form-group">
									<label for="team_root_id" class="is-block">Ομάδα*:</label>
									<select name="team_id" id="team_root_id" style="padding:2px; border:1px solid #000; width:100%;">
										<option value="">--επιλέξτε ομάδα--</option>
<?php
	foreach($this->teams_users AS $team_user){
		echo '<option value="'.$team_user->id.'">'.$team_user->name.'</option>';
	}
?>

									</select>
								</div>
							</div>
<?php
	}
?>
							<div class="form-inline l-fg6 max-600">
								<div class="form-group">
									 <label for="name" class="is-block">Τίτλος δράσης*:</label>
									 <input id="name" name="name" type="text" value="<?php echo @$_REQUEST['name']; ?>" required />
								</div>
								<div class="form-group form-group--upload">
									 <label for="image" class="is-block">Κεντρική φωτογραφία*:</label>
									 <input type="file" name="image" id="image" class="file-browser"  />
									 <span class="is-block is-italic">(μέγιστο επιτρεπόμενο όριο φωτογραφίας: 1MB)</span>
								</div>
							</div>
							<div class="form-group form-inline is-block form--bordered" style="border-bottom: none; padding-bottom:0px; margin-bottom:0px;">
								<label for="short_description" class="is-block">Σύντομη Περιγραφή*:</label>
								<textarea style="max-height: 100px;" class="form-control max-600" maxlength="250" id="short_description" rows="8" name="short_description" required=""><?php echo @$_REQUEST['short_description']; ?></textarea>
								<span class="is-block is-italic">(σύντομη περιγραφή της δράσης σας έως 250 χαρακτήρες)</span>
							</div>
							<div class="form-group form-inline is-block ">
								<label for="activity_description" class="is-block">Περιγραφή*:</label>
								<!--<textarea class="form-control max-600" id="activity_description" rows="8" required=""></textarea>-->
								<?php echo $editor->display('activity_description', @$_REQUEST['activity_description'], '', '200', '20', '20', true, null, null, null, $params_editor); ?>
								<span class="is-block is-italic">(το δελτίο τύπου της δράσης σας)</span>
							</div>
	            			<div class="form-group form--bordered">
								<label class="is-block">Link δράσης:</label>
								<input type="text" name="web_link"  class="input--medium" id="web_link" />
								<span class="is-block is-italic">(https://www.facebook.com/events/123456789/)</span>
							</div>
               <!-- clone start -->
<?php
	for($f=0; $f<11; $f++){
?>
              <div class="form-block" id="form-block-<?php echo $f; ?>" rel="js-form-block" style="display:<?=($f>0?'none':'block')?>">
								<h2>Επιμέρους Δράση <?php echo ($f+1); ?></h2>
								<div class="form-group form--padded--16">
									<label class="is-block">Υπότιτλος δράσης*:</label>
									<input type="text" name="ypotitlos_drashs_<?php echo $f; ?>"  class="input--medium" id="ypotitlos_drashs_<?php echo $f; ?>" <?=($f==0?'required=""':'')?> />

								</div>
	              <div class="form form--padded--16">
	               	<label class="is-block">Τοποθεσία:</label>
	               	<div class="form-inline--cells form-inline v-bottom">
				            <div class="form-group" id="address_fields_<?php echo $f; ?>">
											<label class="is-block">ΔΙΕΥΘΥΝΣΗ:</label>
											<input type="text" name="address_<?php echo $f; ?>" id="address_<?php echo $f; ?>" onclick="openChild('<?php echo JURI::base(); ?>gmap_action2.php','win<?php echo $f; ?>',this.id,'lat_<?php echo $f; ?>','lng_<?php echo $f; ?>')" style="width:80%" />
											<input type="hidden" name="lat_<?php echo $f; ?>" id="lat_<?php echo $f; ?>" value="" />
											<input type="hidden" name="lng_<?php echo $f; ?>" id="lng_<?php echo $f; ?>" value="" />
										</div>
										<div class="form-group stegi_use" >
											<input id="stegi_<?php echo $f; ?>" name="stegi_<?php echo $f; ?>" type="checkbox" data-href="<?php echo $f; ?>">
											<label for="stegi_<?php echo $f; ?>" class="label-horizontal"><small>ΧΡΗΣΗ ΣΤΕΓΗΣ ΣΥΝΑΘΗΝΑ</small></label>
										</div>
									</div>
	              </div>
								<div class="form form--padded--16 show_stegi_hours_<?php echo $f; ?>" id="show_stegi_hours">
									<div class="form-group" >
										<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=37&Itemid=145');?>">ΔΕΙΤΕ ΤΙΣ ΔΙΑΘΕΣΙΜΕΣ ΩΡΕΣ ΤΗΣ ΣΤΕΓΗΣ ΠΡΙΝ ΕΙΣΑΓΕΤΕ ΗΜΕΡΟΜΗΝΙΑ</a>
									</div>
								</div>
	              <div class="form form--padded--16">
	               	<label class="is-block">ΗΜΕΡΟΜΗΝΙΑ*:</label>
	               	<div class="form-inline--cells form-inline v-bottom">
		               	<div class="form-group">
		               		<label for="from_date_<?php echo $f; ?>" class="is-block">Από</label>
		               		<input type="text" class="from_date" id="from_date_<?php echo $f; ?>" name="date_start_<?php echo $f; ?>" <?=($f==0?'required=""':'')?>  />
							<label for="to_date_<?php echo $f; ?>" class="is-block">Έως</label>
		               		<input type="text" class="to_date" id="to_date_<?php echo $f; ?>" name="date_end_<?php echo $f; ?>" <?=($f==0?'required=""':'')?>  />
		               	</div>
										<!--<div class="form-group">
											<input id="box22" type="checkbox">
											<label for="box22" class="label-horizontal"><small>ΕΠΑΝΑΛΑΜΒΑΝΟΜΕΝΗ ΣΤΕΓΗ</small></label>
										</div>-->
	               	</div>
	               </div>
								<div class="form-inline filters form--padded form--bordered">
									<label for="activity_description" class="is-block">Θεματικές δραστηριοποίησης*:</label>
<?php
	foreach($this->team_activities AS $activity){
		echo '<div class="form-group">
						 <input id="activity_'.$activity->id.'_'.$f.'" name="activity_'.$activity->id.'_'.$f.'" type="checkbox" '.($activity->id == 12 ? 'class="remote-option" onclick="showRemote();"' : '').' />
						 <label for="activity_'.$activity->id.'_'.$f.'" class="label-horizontal">'.$activity->name.'</label>
					</div>';
	}
?>
								</div>
								<button type="button" class="add_button btn btn--black btn--add clone" rel="js-add-new-form-block" id="add_<?php echo $f; ?>" onclick="show_hide(<?php echo $f; ?>,1)"></button>
                <button type="button" class="remove_button btn btn--black btn--minus remove is-visuallyhidden" rel="js-remove-new-form-block"  onclick="show_hide(<?php echo $f; ?>,0)" id="remove_<?php echo $f; ?>"></button>
							</div>
<?php
	}
?>
					<!-- clone end -->
							<div class="form form--bordered">
								<div class="form-group">
									<label for="date" class="is-block">Συνεργαζόμενες ομάδες</label>
									<div class="form-group form-inline v-center">
										<select id="tokenize" multiple="multiple" name="teams[]" class="tokenize-sample">
<?php
	foreach($this->teams AS $team){
		echo '<option value="'.$team->id.'" rel="'.JURI::base().($team->logo!=''?$team->logo:'images/template/no-team.jpg').'" id="team_logo_'.$team->id.'">'.$team->name.'</option>';
	}
?>

										</select>
										<span id="logos_select">
										</span>
									</div>
								</div>
								<div class="margin0 form-group form-group--upload">
									 <label for="box70" class="is-block">Φωτογραφίες:</label>
									 <input type="file" name="photos[]" id="box70" class="file-browser" multiple>
									 <span class="is-block is-italic"><small>( μπορείτε να ανεβάσετε την  αφίσα της  δράσης σας, φωτογραφίες
						της τρέχουσας δράσης σας ή και φωτογραφίες από προηγούμενες δράσεις - μέγιστο επιτρεπόμενο όριο φωτογραφίας: 1MB )</small></span>
								</div>
								<div class="form-group" id="services_choice">
									<input id="services" type="checkbox" name="services">
									<label for="services" class="label-horizontal"><small>ΧΡΗΣΗ ΥΠΗΡΕΣΙΩΝ ΔΗΜΟΥ ΑΘΗΝΑΙΩΝ</small></label>
								</div>
								<div class="form-inline filters form--padded" id="service_list" style="display:none">
									<div style="display:block" class="form-inline">
										<label class="is-block">Θα επιθυμούσα:</label>
<?php
	foreach($this->services AS $service){
?>
										<div class="form-group">
											 <input id="<?php echo $service->id; ?>" type="checkbox" name="service_<?php echo $service->id; ?>">
											 <label  style="" for="<?php echo $service->id; ?>" class="label-horizontal"><?php echo $service->name; ?></label>
										</div>
<?php
	}
?>
									</div>
									<div class="form-group is-block form" style="padding-top:12px">
										<label class="is-block">Μήνυμα προς τις υπηρεσίες του δήμου*:</label>
										<textarea name="services_message" class="form-control max-600" id="services_message" rows="8"></textarea>
										<span class="is-block is-italic">(Επεξηγήστε με σαφήνεια τη μορφή υποστήριξης που θέλετε να λάβετε. Το μήνυμά σας θα προωθηθεί στις αρμόδιες δημοτικές υπηρεσίες)</span>
									</div>
								</div>







							</div>
							<div class="form-inline form--bordered filters " rel="">
								<label for="activity_description" class="is-block">Θα επιθυμούσα την υποστήριξη σε:</label>
<?php
		$query = " SELECT id, name "
				." FROM #__team_donation_types
					WHERE published=1 AND parent_id=0	"
				." ORDER BY id ASC ";

		$db->setQuery($query);
		$rows=$db->loadObjectList();
		$messageDivArray = [];
		$i=1;
		$children=array();
        $messageDivArray = [];
		foreach($rows as $row){
			$query = " SELECT id, name "
					." FROM #__team_donation_types
						WHERE published=1 AND parent_id=".$row->id."	"
					." ORDER BY id ASC ";

			$db->setQuery($query);
			$rows1=$db->loadObjectList();

			$messageDivArray[$row->id] = ['name'=>$row->name, 'children' =>[]];
			foreach($rows1 as $row1){
				$children[$i][]=array($row1->id, $row1->name,$row->id);
                array_push($messageDivArray[$row->id]['children'], [$row1->name, $row1->id]);
			}
			echo '<div class="form-group support_fields">
							 <input name="donation-'.$row->id.'" id="donation-'.$row->id.'" type="checkbox" value="show"  '.(@count($children[$i])>0?'data-href="#subcat'.$i.'"':'').'>
							 <label for="donation-'.$row->id.'" class="label-horizontal">'.$row->name.'</label>
						</div>';
			$i++;
		}

		for($i=1; $i<count($children)+1; $i++){
			for($y=0; $y<count($children[$i]); $y++){
				if($y==0){
					echo '<div id="subcat'.$i.'" rel="js-show-category-types" style="display:none;padding-bottom:12px; border-bottom:1px solid #CCC" class="form-block form-inline donation-'.$row->id.'">';
				}
				echo '<div class="form-group">
								<input name="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'" id="donation-'.$children[$i][$y][2].'-'.$children[$i][$y][0].'"  type="checkbox">
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

                <?php
                foreach($messageDivArray as $parentKey => $subArray) {
                    if(count($subArray['children'])){
                        foreach($subArray['children'] as $child) { ?>
                            <div class="form-group form--padded donation-message child-donation-message textarea-donation-<?php echo $parentKey."-".$child[1] ?>" style="display:none">
                                <label for="support_message" class="is-block">Μήνυμα προς <?php echo $child[0] ?>*:</label>
                                <textarea class="form-control max-600" id="support_message-<?php echo $parentKey."-".$child[1] ?>" rows="8" name="support_message-<?php echo $parentKey."-".$child[1] ?>" ></textarea>
                            </div>
                        <?php }
                    } else { ?>
                      <div class="form-group form--padded donation-message parent-donation-message textarea-donation-<?php echo $parentKey ?>" style="display:none">
                            <label for="support_message" class="is-block">Μήνυμα προς <?php echo $subArray['name']; ?>*:</label>
                            <textarea class="form-control max-600" class="support_message" id="support_message-<?php echo $parentKey ?>" rows="8" name="support_message-<?php echo $parentKey ?>" ></textarea>
                        </div>
                    <?php }
                }
                ?>
                <div class="clearfix"></div>
				<div class="form-group form--padded hidden" id="remote-checkbox">
					<input id="remote" type="checkbox" name="remote">
					<label for="remote" class="label-horizontal"><small>*Δέχομαι να καταχωρηθεί η δράση μου στο accmr.gr</small></label>
				</div>
               <div class="form-group form-group--tail is-block clearfix" style="clear:both">
                  <span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
                  <button type="submit" class="pull-right btn btn--coral btn--bold">Καταχώριση</button>
               </div>
							<input type="hidden" name="option" value="com_actions" />
							<input type="hidden" name="task" value="form.save" />
							<input type="hidden" name="team_id" id="team_id" value="<?php echo @$initial_team->id; ?>" />
							<input type="hidden" name="user_id" id="user_id" value="<?php echo @$initial_team->user_id; ?>" />
							<input type="hidden" name="return" value="<?php echo JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143&action_save=1');?>" />
							<input type="hidden" name="return_false" value="<?php echo JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143&action_error=1');?>" />
							<input type="hidden" name="newform" value="<?php echo $newform_session; ?>" />

							<?php echo JHtml::_('form.token');?>
            </form>
        </div>
      </div>
    </div>
</div>
<?php
}
?>
