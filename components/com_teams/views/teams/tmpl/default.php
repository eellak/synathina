<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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

$teamtext_modules=JModuleHelper::getModules('teams_text');
$params_teamtext   = array('style'=>'teamtext');

?>
<form team="<?php echo JURI::current(); ?>" method="get" class="form" rel="js-teams-form">
<div class="l-draseis">

   <div class="module module--synathina">
      <div class="module-skewed module-skewed--gray">
         <div class="module-wrapper">
<?php
		foreach ($teamtext_modules as $teamtext_module){
			echo JModuleHelper::renderModule($teamtext_module, $params_teamtext);
		}
?>				 
         </div>
      </div>
   </div>

   <a href="<?php echo JRoute::_('index.php?option=com_teams&view=form&Itemid=139');?>" class="btn btn--skewed btn--coral btn--bold"><strong>Ανέβασε τη δράση σου</strong></a>

   <!--<ul class="inline-list inline-list--separated inline-list--headlines">
      <li class=""><a href="">Ημερολόγιο</a></li>
      <li><a href="" class="selected">Αναζήτηση</a></li>
   </ul>-->

   <div class="filters">
      <div class="filter-search">
         
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Αναζήτηση για..." value="<?php echo @$_REQUEST['search_name']; ?>" />
                <span class="input-group-btn">
                     <button class="btn btn-search" type="submit"><i class="fa fa-search"></i></button>
                </span>
              </div><!-- /input-group -->
         
      </div>
<?php
	for($i=1; $i<8; $i++){
		echo '<div class="form-group">
						 <input id="box'.$i.'" name="area'.$i.'" '.(@$_REQUEST['area'.$i]=='on'?'checked="checked"':'').' type="checkbox" onclick="this.form.submit()" />
						 <label for="box'.$i.'" class="label-horizontal">'.$i.'o Διαμέρισμα</label>
					</div>';
	}
?>			
      <div class="block"></div>
      <div class="form-group">
         <label for="">Από:</label>
         <input type="text" class="form-control" name="from" value="<?php echo @$_REQUEST['from']; ?>" rel="js-datepicker-from">
      </div>
      <div class="form-group">
         <label for="">Έως:</label>
         <input type="text" class="form-control" name="to" value="<?php echo @$_REQUEST['to']; ?>" rel="js-datepicker-to">
      </div>
      <div class="form-group">
         <input id="box8" type="checkbox" onclick="this.form.submit()" name="best" <?=(@$_REQUEST['best']=='on'?'checked="checked"':'')?>>
         <label for="box8" class="label-horizontal">BEST PRACTICE</label>
      </div>
      <div class="block"></div>
<?php
	foreach($this->activities as $activity){
    echo '<div class="form-group">
						<input id="box_activity_'.$activity->id.'" name="activity'.$activity->id.'" '.(@$_REQUEST['activity'.$activity->id]=='on'?'checked="checked"':'').' type="checkbox" onclick="this.form.submit()" />
						 <label for="box_activity_'.$activity->id.'" class="label-horizontal">'.$activity->name.'</label>
					</div>';		
	}
?>	
   </div>
	 <input type="hidden" />
   <!-- EOF Filters -->
   <div class="filter-results">
<?php
	if(count($this->items)==0){
		echo 'Δε βρέθηκαν αποτελέσματα αναζήτησης.';
	}
	foreach($this->items as $team){
		$team_date_start_array=explode(' ',$team->team_date_start);
		$team_date_start_array1=explode('-',$team_date_start_array[0]);
		$date_from=$team_date_start_array1[2].' '.$months[(int)$team_date_start_array1[1]].' '.$team_date_start_array1[0];
		$team_date_end_array=explode(' ',$team->team_date_end);
		$team_date_end_array1=explode('-',$team_date_end_array[0]);
		$date_to=$team_date_end_array1[2].' '.$months[(int)$team_date_end_array1[1]].' '.$team_date_end_array1[0];	
		
		$link=JRoute::_('index.php?option=com_teams&view=team&id='.$team->aid.'&Itemid='.@$_REQUEST['Itemid']);
		$date_start_array=explode(' ',$team->team_date_start);
		echo '<div class="media">
						<div class="media-left">';
		if($team->aimage!=''){
			echo '	<a href="'.$link.'"><img style="max-width:268px" src="images/teams/main_images/'.$team->aimage.'" alt=""></a>';
		}
		echo '	 </div>
						 <div class="media-body">
								<h3 class="media-title">'.stripslashes($team->subtitle).'</h3>
								<span class="week-margin">'.$date_from.' – '.$date_to.'</span>
								<p>'.stripslashes($team->short).' <a href="'.$link.'" class="more">Περισσότερα</a></p>
						 </div>
					</div>';
	}
?>	

   </div>
   <div class="inline-child-center">
			<?php echo $this->pagination->getPagesLinks(); ?>
   </div>

   <h2>Εγχείριδια δράσεων / Toolkits</h2>
   <ul class="inline-list inline-list--separated inline-list--headlines">
      <li><a href="">2015</a></li>
      <li class="selected"><a href="">2016</a></li>
   </ul>
   <div class="documents-list">
      <div class="documents-list-row">
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
         <a href="" class="documents-list-item">
            <i class="fill" style="background-image: url(http://placehold.it/233x261)"></i>
         </a>
      </div>
   </div>
</div>
</form>

<?php
//meta tags
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuname = $menu->getActive()->title;
$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';
$document = JFactory::getDocument();
$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'συνΑθηνά' );
$document->setMetaData( 'twitter:description', $menuname );
$document->setMetaData( 'twitter:image', $article_image );
?>