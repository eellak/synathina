<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

//db connection
$db = JFactory::getDBO();

$config= new JConfig();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

$months=array(1=>'ΙΑΝ','ΦΕΒ','ΜΑΡ','ΑΠΡ','ΜΑΙ','ΙΟΥΝ','ΙΟΥΛ','ΑΥΓ','ΣΕΠ','ΟΚΤ','ΝΟΕ','ΔΕΚ');

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$actiontext_modules=JModuleHelper::getModules('actions_text');
$params_actiontext   = array('style'=>'actiontext');
$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');
?>
<div class="l-register l-mydraseis show-profile">
<?php
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
?>
	<div class="module module--synathina">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register" style="padding: 0 20px;">
				<h3 class="popup-title">Ο λογαριασμός μου</h3>
<?php
	if(@$_REQUEST['action_save']==1 && !$isroot){
		echo '<p><strong>Αγαπητέ χρήστη, η δράση σας έχει υποβληθεί στο www.synathina.gr. Εντός 48 ωρών, θα λάβετε ειδοποίηση για τη δημοσίευτη της δράσης σας. Σας ευχαριστούμε, Η ομάδα του συνΑθηνά.</strong></p>';
	}

	if(@$_REQUEST['action_error']==1 && !$isroot){
		echo '<p style="color: red"><strong>Παρουσιάστηκε σφάλμα κατά την καταχώριση, προσπαθήστε ξανά.</strong></p>';
	}
?>
   <!-- EOF Filters -->
   <div class="filter-results">
<?php
	if(count($this->items)==0){
		echo 'Δεν έχετε ανεβάσει δράσεις.';
	}
	foreach($this->items as $action){
		//$action_date_start_array=explode(' ',$action->action_date_start);
		//$action_date_start_array1=explode('-',$action_date_start_array[0]);
		//$date_from=$action_date_start_array1[2].' '.$months[(int)$action_date_start_array1[1]].' '.$action_date_start_array1[0];
		//$action_date_end_array=explode(' ',$action->action_date_end);
		//$action_date_end_array1=explode('-',$action_date_end_array[0]);
		//$date_to=$action_date_end_array1[2].' '.$months[(int)$action_date_end_array1[1]].' '.$action_date_end_array1[0];

		$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->id.'&Itemid='.@$_REQUEST['Itemid']);
		$link1=JRoute::_('index.php?option=com_actions&view=edit&id='.$action->id.'&Itemid=144');
		//$date_start_array=explode(' ',$action->action_date_start);
		echo '<div class="media" style="margin-top: 0px; width:auto">
						<div class="media-left">';
		if($action->image!=''){
			echo '	<a href="'.$link.'"><img class="results-img-width" src="images/actions/main_images/'.$action->image.'" alt=""></a>';
		}
		echo '	 </div>
						 <div class="media-body">
								<h3 class="media-title">'.stripslashes($action->name).'</h3>
								<!--<span class="week-margin">'.@$date_from.' – '.@$date_to.'</span>-->
								<p>'.stripslashes($action->short_description).' <br />'.($action->published==1?'<a href="'.$link.'" class="more">Περισσότερα</a>&nbsp;&nbsp;&nbsp;':'').'<a href="'.$link1.'" class="more">Επεξεργασία</a></p>
						 </div>
					</div>';
	}
?>

   </div>
   <div class="inline-child-center">
			<?php echo $this->pagination->getPagesLinks(); ?>
   </div>
	 </div>
	 </div>
	 </div>
</div>
