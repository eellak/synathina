<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//db connection
$db = JFactory::getDBO();

$document = JFactory::getDocument();

$config = JFactory::getConfig();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuname = $menu->getActive()->title;
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

$months=array(1=>'ΙΑΝ','ΦΕΒ','ΜΑΡ','ΑΠΡ','ΜΑΙ','ΙΟΥΝ','ΙΟΥΛ','ΑΥΓ','ΣΕΠ','ΟΚΤ','ΝΟΕ','ΔΕΚ');

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

$actiontext_modules=JModuleHelper::getModules('actions_text');
$toolkits_modules=JModuleHelper::getModules('toolkits');
$params_actiontext   = array('style'=>'actiontext');

function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
        }
    }
    return 'th';
}
?>
<div class="module module--synathina popup popup--small mfp-hide" id="popup-message" style="margin: 0px auto;">
   <div class="module-skewed">
		<div class="popup popup--questionmark">
			<button class="close"><i class="fa fa-close"></i></button>
			<div class="popup__content">

				<p>H ομάδα του συνΑθηνά εντοπίζει τις δράσεις με την υψηλότερη επιρροή στην πόλη και αναδεικνύει τις βέλτιστες πρακτικές (best practices).
				</p>
			</div>
		</div>
	</div>
</div>
<form action="<?php echo JURI::current(); ?>#actions-results" method="get" class="form" id="search_actions" rel="js-actions-form">
<div class="l-draseis">

   <div class="module module--synathina">
      <div class="module-skewed module-skewed--gray">
         <div class="module-wrapper">
<?php
		foreach ($actiontext_modules as $actiontext_module){
			echo JModuleHelper::renderModule($actiontext_module, $params_actiontext);
		}
?>
         </div>
      </div>
   </div>

   <a href="<?php echo JRoute::_('index.php?option=com_actions&view=form&Itemid=139');?>" class="btn btn--skewed btn--coral btn--bold"><strong><?=($lang_code=='en'?'Create Activity':'Ανέβασε τη δράση σου')?></strong></a>

   <!--<ul class="inline-list inline-list--separated inline-list--headlines">
      <li class=""><a href="">Ημερολόγιο</a></li>
      <li><a href="" class="selected">Αναζήτηση</a></li>
   </ul>-->

   <div class="filters">
      <div class="filter-search">
				<div class="input-group">
					<input type="text" class="form-control" name="search_name" placeholder="<?=($lang_code=='en'?'Search...':'Αναζήτηση για...')?>" value="<?php echo strip_tags(@$_REQUEST['search_name']); ?>" />
				</div><!-- /input-group -->
      </div>
			<div class="block"></div>
<?php
	for($i=1; $i<8; $i++){
		echo '<div class="form-group">
						 <input id="box'.$i.'" name="area'.$i.'" '.(@$_REQUEST['area'.$i]=='on'?'checked="checked"':'').' type="checkbox" />
						 <label for="box'.$i.'" class="label-horizontal">'.$i.($lang_code=='en'?ordinal_suffix($i):'η').' '.($lang_code=='en'?'District':'Δημοτική Κοινότητα').'</label>
					</div>';
	}
?>
      <div class="block"></div>
      <div class="form-group">
         <label for=""><?=($lang_code=='en'?'From':'Από')?>:</label>
         <input type="text" class="form-control" name="from" value="<?php echo strip_tags(@$_REQUEST['from']); ?>" rel="js-datepicker-from" readonly="true" />
      </div>
      <div class="form-group">
         <label for=""><?=($lang_code=='en'?'To':'Έως')?>:</label>
         <input type="text" class="form-control" name="to" value="<?php echo strip_tags(@$_REQUEST['to']); ?>" rel="js-datepicker-to" readonly="true" />
      </div>
      <div class="form-group" style="margin-right: 0px;">
				<input id="box8" type="checkbox" name="best" <?=(@$_REQUEST['best']=='on'?'checked="checked"':'')?>>
			  <label for="box8" class="label-horizontal">BEST PRACTICE</label>
      </div>
			<div class="form-group">
				<a class="form-tooltip" href="#popup-message" style="color: #05c0de;display: inline;"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
			</div>
      <div class="block"></div>
<?php
	foreach($this->team_activities as $team_activity){
    echo '<div class="form-group">
						<input id="box_activity_'.$team_activity->id.'" name="activity'.$team_activity->id.'" '.(@$_REQUEST['activity'.$team_activity->id]=='on'?'checked="checked"':'').' type="checkbox" />
						 <label for="box_activity_'.$team_activity->id.'" class="label-horizontal">'.($lang_code=='en'?$team_activity->name_en:$team_activity->name).'</label>
					</div>';
	}
?>
		<div class="block"></div>
		<button type="submit" style="padding: 5px 10px;" class="pull-right btn btn--coral btn--bold btn btn-primary validate"><?=($lang_code=='en'?'SEARCH':'ΑΝΑΖΗΤΗΣΗ')?></button>
		<button type="reset" onclick="document.getElementById('search_actions').reset();$('#search_actions input[type=checkbox]').attr('checked',false);" style="padding: 5px 10px; margin-right:10px;" class="pull-right btn btn--grey btn--bold btn btn-primary validate"><?=($lang_code=='en'?'RESET':'ΚΑΘΑΡΙΣΜΟΣ')?></button>
		<div class="block" style="clear:both"></div>
   </div>

   <!-- EOF Filters -->
   <div class="filter-results" id="actions-results">
<?php
	if(count($this->items)==0){
		if($lang_code=='en'){
			echo 'No results';
		}else{
			echo 'Δε βρέθηκαν αποτελέσματα αναζήτησης.';
		}

	}
	$c=1;
	foreach($this->items as $action){
		$action_date_start_array=explode(' ',$action->action_date_start);
		$action_date_start_array1=explode('-',$action_date_start_array[0]);
		$date_from=$action_date_start_array1[2].' '.$months[(int)$action_date_start_array1[1]].' '.$action_date_start_array1[0];
		$action_date_end_array=explode(' ',$action->action_date_end);
		$action_date_end_array1=explode('-',$action_date_end_array[0]);
		$date_to=$action_date_end_array1[2].' '.$months[(int)$action_date_end_array1[1]].' '.$action_date_end_array1[0];

		//$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->aid.'&Itemid='.@$_REQUEST['Itemid']);
		$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->aid.':'.$action->alias.'&Itemid='.@$_REQUEST['Itemid']);
		$date_start_array=explode(' ',$action->action_date_start);
		echo '<div class="media">
						<div class="media-left badge-item" >';
		if($action->aimage!=''){
			echo '	<div class="badge-image"><a href="'.$link.'"><img class="results-img-width" src="'.($action->origin == 2 ? $config->get('remote_site') : '').'/images/actions/main_images/'.$action->aimage.'" alt="" /></a></div>';
			if(@$action->best_practice==1){
				echo '	<div class="badge-icon"><a href="'.$link.'"><img class="results-img-height" src="images/template/best.png" alt="" /></a></div>';
			}
		}
		echo '	 </div>
						 <div class="media-body">
								<h3 class="media-title"><a href="'.$link.'">'.stripslashes($action->subtitle).'</a>'.($isroot==1&&$action->apublished==0?' <span style="color:red;">ανενεργό</span>':'').'</h3>
								<span class="week-margin">'.($date_from!=$date_to?$date_from.' – '.$date_to:$date_from).'</span>
								<p>'.stripslashes($action->short).' <a href="'.$link.'" class="more">Περισσότερα</a></p>
						 </div>
					</div>';
		if($c%2==0){
			echo '<div class="articles_by_two">&nbsp;</div>';
		}
		$c++;
	}
?>

   </div>
   <div class="inline-child-center">
			<?php echo $this->pagination->getPagesLinks(); ?>
   </div>
<?php
		foreach ($toolkits_modules as $toolkits_module){
			echo JModuleHelper::renderModule($toolkits_module);
		}
?>
</div>
</form>

<?php
//meta tags
$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';
$document = JFactory::getDocument();
$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'συνΑθηνά' );
$document->setMetaData( 'twitter:description', $menuname );
$document->setMetaData( 'twitter:image', $article_image );
?>