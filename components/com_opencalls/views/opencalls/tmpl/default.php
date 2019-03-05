<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//db connection
$db = JFactory::getDBO();
$user = JFactory::getUser();
		
$config= new JConfig();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
require_once JPATH_CONFIGURATION.'/global_functions.php';

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
	<div class="module module--synathina account-list">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register" style="padding: 0 20px;">
				<h3 class="popup-title">Open Calls</h3>				
					<!-- EOF Filters -->
					<div class="filter-results">
<?php
	if(count($this->items)==0){
		echo 'Δεν έχετε ανεβάσει open calls.';
	}
	foreach($this->items as $opencall){
		$query = "SELECT i.object_image_id,i.filename 
							FROM #__di_images AS i
							WHERE i.state=1 AND i.object_id='".$opencall->id."' ORDER BY i.ordering ASC LIMIT 1 ";
		$db->setQuery( $query );
		$images = $db->loadObjectList();	
		$opencall->image='';		
		foreach($images as $image){
			$opencall->image='images/di/'.$opencall->id.'_'.$image->object_image_id.'_'.$image->filename;
		}
		
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($opencall->id.':'.$opencall->alias, $opencall->catid, $opencall->language));
		$link1=JRoute::_('index.php?option=com_opencalls&view=edit&id='.$opencall->id.'&Itemid=171');
		echo '<div class="media" style="margin-top: 0px; width:auto">
						<div class="media-left">';
		if($opencall->image!=''){
			echo '	<a href="'.$link.'"><img class="results-img-width" src="'.$opencall->image.'" alt=""></a>';
		}
		echo '	 </div>
						 <div class="media-body">
								<h3 class="media-title">'.stripslashes($opencall->title).'</h3>
								'.get_first_num_of_words($opencall->introtext,100).' <br /><a href="'.$link.'" class="more">Περισσότερα</a>&nbsp;&nbsp;&nbsp;<a href="'.$link1.'" class="more">Επεξεργασία</a>
						 </div>
					</div>';
	}
?>	

				</div>
			</div>
		</div>
	</div>
</div>
