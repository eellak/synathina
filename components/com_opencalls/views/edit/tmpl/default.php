<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//session in form to prevent double submits
$session = JFactory::getSession();
$user = JFactory::getUser();
$session->set( 'editform', md5($user->id.'-'.time()) );
$editform_session=$session->get( 'editform' );

if($user->id==''){
	header('Location:'.JRoute::_('index.php?option=com_users&view=login'));
	exit();
}

//db connection
$db = JFactory::getDBO();
$config = JFactory::getConfig();

$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');

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
<?php
	foreach ($breadcumbs_modules as $breadcumbs_module){
		echo JModuleHelper::renderModule($breadcumbs_module);
	}
	foreach ($this->opencalls as $opencall){
?>
   <div class="module module--synathina">
      <div class="module-skewed">
         <!-- Content Module -->
         <div class="register">
            <h3 class="popup-title">Επεξεργασία Open Call:</h3>
            <form action="<?php echo JURI::current();?>" class="form" method="post" name="edit_opencall" id="edit_opencall_edit" enctype="multipart/form-data">
							<div class="form-group">
								 <label class="is-block"for="">Τίτλος*:</label>
								 <input type="text" class="input--large" name="open_call_title" id="open_call_title_edit" value="<?php echo $opencall->title; ?>" required>
								 <span class="is-block is-italic">(πχ. Ανοιχτό Κάλεσμα για Εθελοντές από την ομάδα Άλφα)</span>
							</div>
							<div class="form-group">
								 <label for="open_call_description" class="is-block">Περιγραφή*:</label>
								 <?php echo $editor->display('open_call_description', $opencall->introtext, '100%', '200', '', '20', true, null, null, null, $params_editor); ?>
								 <span class="is-block is-italic">(πχ. Αναγράφετε όλα τα στοιχεία του δελτίου τύπου)</span>
							</div>
							<div class="form-inline filters">
								<label for="" class="is-block">Θεματική ενότητα*:</label>
<?php
	$attribs=json_decode($opencall->attribs);
	$opencall_activities=$attribs->opencall_activities;
	$opencall_date=$attribs->opencall_date;
	$opencall_date_array=explode('-',$opencall_date);
	$opencall_date_formated=$opencall_date_array[2].'/'.$opencall_date_array[1].'/'.$opencall_date_array[0];

	foreach($this->activities as $activity){
    echo '<div class="form-group">
						<input id="box_activity_edit_'.$activity->id.'" name="activities['.$activity->id.']" '.(in_array($activity->id,$opencall_activities)?'checked="checked"':'').' type="checkbox" />
						<label for="box_activity_edit_'.$activity->id.'" class="label-horizontal">'.($lang_code=='en'?$activity->name_en:$activity->name).'</label>
					</div>';
	}
?>
							</div>
							<div class="form-inline" style="vertical-align: top">
								 <div class="form-group" style="vertical-align: top">
										<label for="date_end" class="is-block">Ημερομηνία λήξης:</label>
										<input type="text" id="opencall_date_edit" name="open_call_date" value="<?php echo $opencall_date_formated;?>" required>
								 </div>
								</div>
								<div class="form-inline" style="vertical-align: top">
								 <div class="form-group" style="vertical-align: top">
										<label for="upload" class="is-block">Σχετικά αρχεία:</label>
										<div style="vertical-align:top; margin-bottom:10px;" class="clearfix">
<?php
	$p=0;
	foreach ($this->images as $image) {
		$photo='images/di/'.$image->object_id.'_'.$image->object_image_id.'_'.$image->filename;
		echo '<div id="photo_edit_'.$p.'" style="display:block; position:relative; float:left; vertical-align:top;width:80px; height:80px; margin-right:2px;">
						<img style="position:absolute; top:0px; left:0px; width:80px; height:80px; vertical-align:top" src="'.$photo.'" alt=""/>
						<a title="διαγραφή" onclick="delete_di_confirmation(\''.$image->object_id.'\',\''.$image->object_image_id.'\',\''.$image->filename.'\',\''.$p.'\',\''.JUri::base().'\')" href="javascript:void(null)" style="position:absolute; right:5px; top 5px;color:red">x</a>
					</div>';
		$p++;
	}
	echo '<div style="clear:both;height:18px;"></div>';
	if(count($this->files)>0){
?>
											<div class="document-download"><ul class="inline-list">
<?php
		foreach($this->files as $file){
			$icon_filename=substr($file->icon_filename,0,3);
?>
												<li id="attachment_<?php echo $file->id;?>">
													<a target="blank" href="<?php echo $file->url;?>"><i class="doc-icon doc-icon--<?php echo $icon_filename; ?>"></i><div><?=($file->display_name!=''?stripslashes($file->display_name):$file->filename);?></div></a>
													<a title="διαγραφή" onclick="delete_file_confirmation('<?php echo $user->id;?>','<?php echo $file->id;?>','<?php echo $file->url;?>','<?php echo JUri::base();?>')" href="javascript:void(null)" style="color:red">x</a>
												</li>
<?php
		}
?>
											</ul></div>
<?php
	}
?>
										</div>
										<input type="file" name="files[]" id="upload_edit" placeholder="Παρακαλώ επιλέξτε αρχείο" multiple="multilple" class="file-browser" multiple>
										<span class="is-block is-italic">(Μπορείτε να επισυνάψετε το δελτίο τύπου  του  καλέσματός σας ή την αφίσα ή κάποιες σχετικές φωτογραφίες.)</span>
								 </div>
							</div>
							<div class="form-group form-group--tail is-block clearfix">
								 <span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
								 <button type="submit" class="pull-right btn btn--coral btn--bold">Καταχώριση</button>
							</div>
							<input type="hidden" name="option" value="com_opencalls" />
							<input type="hidden" name="task" value="edit.save" />
							<input type="hidden" name="id" value="<?php echo $opencall->id; ?>" />
							<input type="hidden" name="editform" value="<?php echo $editform_session; ?>" />
							<input type="hidden" name="return" value="<?php echo JRoute::_('index.php?option=com_opencalls&view=opencalls&Itemid=170');?>" />
							<?php echo JHtml::_('form.token');?>
            </form>
        </div>
      </div>
    </div>
<?php
	}
?>
</div>
