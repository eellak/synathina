<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$action = $this->action;

$document = JFactory::getDocument();
$document->setTitle($action->name);

function strip_single_tag($str,$tag){

    $str1=preg_replace('/<\/'.$tag.'>/i', '', $str);

    if($str1 != $str){

        $str=preg_replace('/<'.$tag.'[^>]*>/i', '', $str1);
    }

    return $str;
}


//db connection
$db = JFactory::getDBO();

//remote db
$dbRemoteClass = new RemotedbConnection();
$db_remote = $dbRemoteClass->remoteConnect();

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

$activities = $this->team_activities;
$activities_array_info=array();
foreach($activities as $activity){
	$activities_array_info[$activity->id]=array($activity->name, $activity->image);
}

if ($action->origin == 1) {
	$query = "SELECT t.id, t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
				WHERE t.id='".$action->team_id."' LIMIT 1";
	$db->setQuery($query);
	$team = $db->loadObject();
} else {
	$query = "SELECT t.id, t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
				WHERE t.id='".$action->accmr_team_id."' LIMIT 1";
	$db_remote->setQuery($query);
	$team = $db_remote->loadObject();
}

$months=array(1=>'ΙΑΝ','ΦΕΒ','ΜΑΡ','ΑΠΡ','ΜΑΙ','ΙΟΥΝ','ΙΟΥΛ','ΑΥΓ','ΣΕΠ','ΟΚΤ','ΝΟΕ','ΔΕΚ');

$config = JFactory::getConfig();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

//get menu clues
$menu_params = $app->getMenu()->getActive()->params;
$menu_link = $app->getMenu()->getActive()->link;

//get live site url
$live_site = $config->get('live_site');
if ($action->origin == 2) {
	$live_site = $config->get('remote_site');
}

$subactions=$this->subactions;

?>

<div class="l-draseis l-draseis--article">

<?php
	if($isroot==1 && $action->origin==1){
		echo '<a href="index.php?option=com_actions&view=edit&id='.$action->id.'&Itemid=144" style="font-weight:bold; font-size:20px; color:#05c1df">edit</a>';
	}
?>
   <div class="clearfix">

      <aside class="l-draseis__col-left">
<?php
	if($action->image!=''){
?>
    <img src="<?php echo $live_site; ?>/images/actions/main_images/<?php echo $action->image; ?>" alt="<?php echo $action->name; ?>" style="width:100%; " />
<?php
	}
?>
         <div class="module module--synathina">
            <div class="module-skewed module-skewed gray">
               <div class="list-group list-group--draseis">
                  <div class="list-group-item">
<?php
			if(@$action->best_practice==1){
				echo '	<img style="max-width:56px" src="images/template/best.png" alt="" />';
			}
?>
                     <h3 class="list-group-item-title" style="<?=(@$action->best_practice==1?'padding-top:0px;':'')?>"><?php echo $action->name; ?></h3>
                     <ul class="inline-list inline-list--separated">
                        <li>
                        	<a href="<?php echo ($action->origin == 2 ? $config->get('remote_site') : '').JRoute::_('index.php?option=com_teams&view=team&id='.$team->id.'&Itemid=137');  ?>" <?php echo ($action->origin == 2 ? 'target="_blank"' : ''); ?>>
                        		<?php echo $team->tname; ?>
                        	</a>
                        </li>
                     </ul>
<?php
	if ($action->partners && $action->origin == 1) {
		$query = "SELECT name, id FROM #__teams
					WHERE id IN (".rtrim($action->partners, ',').") AND published=1";
		$db->setQuery($query);
		$partners = $db->loadObjectList();

		echo '<br />Συνεργαζόμενες ομάδες:<br /><ul class="inline-list inline-list--separated">';
		foreach ($partners as $partner) {
			echo '<li><a href="'.JRoute::_('index.php?option=com_teams&view=team&id='.$partner->id.'&Itemid=137').'">'.$partner->name.'</a></li>';
		}
		echo '</ul>';
	}

	if ($action->supporters && $action->origin == 1) {
		$query = "SELECT name, id FROM #__teams
					WHERE id IN (".rtrim($action->supporters, ',').") AND published=1";
		$db->setQuery($query);
		$supporters = $db->loadObjectList();

		echo '<br />Υποστηρικτές:<br /><ul class="inline-list inline-list--separated">';
		foreach ($supporters as $supporter) {
			echo '<li><a href="'.JRoute::_('index.php?option=com_teams&view=team&id='.$supporter->id.'&Itemid=137').'">'.$supporter->name.'</a></li>';
		}
		echo '</ul>';
	}

	if($action->web_link!=''){
		$web_link1=$action->web_link;
		$prefix=substr($action->web_link,0,4);
		if($prefix!='http'){
			$web_link1='http://'.$action->web_link;
		}
?>
                     <a class="list-group-link" href="<?php echo $web_link1; ?>" target="_blank" style="text-decoration:underline">
                        Web Link / Facebook Event
                     </a>
<?php
	}
?>
                  </div>
<?php
	foreach($subactions as $subaction){
		$action_date_start_array=explode(' ',$subaction->action_date_start);
		$action_date_start_array1=explode('-',$action_date_start_array[0]);
		$time_array_start=explode(':',$action_date_start_array[1]);
		$date_from=$action_date_start_array1[2].' '.$months[(int)$action_date_start_array1[1]].' '.$action_date_start_array1[0];
		$date_from1=$action_date_start_array1[2].' '.$months[(int)$action_date_start_array1[1]].' '.$action_date_start_array1[0].' '.$time_array_start[0].':'.$time_array_start[1];
		$time_from=$time_array_start[0].':'.$time_array_start[1];
		$action_date_end_array=explode(' ',$subaction->action_date_end);
		$action_date_end_array1=explode('-',$action_date_end_array[0]);
		$time_array_end=explode(':',$action_date_end_array[1]);
		$date_to=$action_date_end_array1[2].' '.$months[(int)$action_date_end_array1[1]].' '.$action_date_end_array1[0];
		$date_to1=$action_date_end_array1[2].' '.$months[(int)$action_date_end_array1[1]].' '.$action_date_end_array1[0].' '.$time_array_end[0].':'.$time_array_end[1];
		$time_to=$time_array_end[0].':'.$time_array_end[1];
?>
                  <div class="list-group-item">
                     <h4 class="list-group-item-title list-group-item-title--subtitle"><?php echo $subaction->subtitle; ?></h4>
                     <span><strong><?=($date_from!=$date_to?$date_from1.' – '.$date_to1:$date_from.' '.$time_from.'-'.$time_to)?></strong></span>
                     <br>
<?php
	if($subaction->stegi_use==1){
		echo '<a style="color:#5d5d5d" href="http://www.google.com/maps/place/37.980522,23.726839/@37.980522,23.726839,15z" target="_blank">Αθηνάς 55, στέγη συνΑθηνά</a>';
	}else{
		echo '<a style="color:#5d5d5d" href="http://www.google.com/maps/place/'.$subaction->lat.','.$subaction->lng.'/@'.$subaction->lat.','.$subaction->lng.',15z" target="_blank">'.$subaction->address.'</a>';
	}
?>
                     <br>
                     <ul class="inline-list inline-list--separated thematiki--list">
<?php
 	$activities_array=explode(',',$subaction->activities);
 	for($i=0; $i<count($activities_array); $i++){
		if($activities_array[$i]!=''){
			echo '	<div class="thematiki thematiki--perivallon">
						<img src="'.$activities_array_info[$activities_array[$i]][1].'" width="45" height="35" alt="'.$activities_array_info[$activities_array[$i]][0].'" title="'.$activities_array_info[$activities_array[$i]][0].'" />
					</div>';
		}
	}
 	if ($subaction->origin == 2) {
			echo '	<div class="thematiki thematiki--perivallon">
						<img src="images/activities/11.png" width="45" height="35" alt="ΜΕΤΑΝΑΣΤΕΣ & ΠΡΟΣΦΥΓΕΣ" title="ΜΕΤΑΝΑΣΤΕΣ & ΠΡΟΣΦΥΓΕΣ" />
					</div>';
 	}
?>
                     </ul>
<?php if ($subaction->area) { ?>
                     <br>
                     <span><strong><?php echo $subaction->area; ?><sup><?php echo ($lang_code == 'en' ? $english_suffixes[$subaction->area] : 'η'); ?></sup> <?php echo ($lang_code == 'en' ? 'District' : 'Δημοτική Κοινότητα'); ?></strong></span>
<?php } ?>
                  </div>
<?php
	}
?>
               </div>
            </div>
         </div>
      </aside>
      <div class="l-draseis__col-right">
			<article class="c-article">
<?php
	$desc=$action->description;
	if($action->description==''){
		$desc=$action->short_description;
	}
	$desc = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $desc);
	$replace_array=array('<div>','</div>','<a ');
	$replace_array1=array('<p>','</p>','<a target="_blank" ');
	echo str_replace($replace_array,$replace_array1,strip_tags($desc,'<a><p><br><strong><h3>'));
?>
			</article>
			<div class="pull-right share-icons">
				<a href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $action->name;?>&amp;p[summary]=<?php echo addslashes(strip_tags($desc));?>&amp;p[url]=<?php echo JUri::current();?>&amp;p[images][0]=<?php echo 'http://www.synathina.gr/images/actions/main_images/'.$action->image; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600'); return false;">
					<img src="<?php echo JURI::base(); ?>images/template/facebook.png" alt="facebook" />
				</a>&nbsp;
				<a href="http://twitter.com/home?status=<?php echo clean(str_replace('|', '', $action->name)); ?> <?php echo urlencode(JUri::current());?>"  onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600');return false;">
					<img src="<?php echo JURI::base(); ?>images/template/tweet.png" alt="twitter" />
				</a>
			</div>
      </div>

   </div>


<?php
	if ($action->origin == 1) {
		$images = [];
		foreach (glob('images/actions/'.$action->id.'/*.*') as $filename) {
			$images[]=$filename;
		}
	} else {
		$postdata = http_build_query (
		    array (
		        'action_id' => $action->id
		    )
		);
		$opts = array('http' =>
		    array(
		        'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);
		$context  = stream_context_create($opts);
		$images = json_decode(file_get_contents($config->get('remote_site') .'/action_images.php', false, $context));
	}
	$i = count($images);
	$p = 1;
	if ($images) {
		echo '	<div class="module module--synathina">
					<h3 class="gallery-title">Gallery</h3>
					<div class="gallery gallery--multirow" rel="js-start-gallery">';
		for ($c = 1; $c <= $i; $c++) {
			if ($c == 1 || ($c - 1) % 6 == 0) {
				echo '	<div class="gallery-frame" data-id="'.$p.'">';
				$p++;
			}
			echo '			<div class="gallery-item">
								<a class="fill" class="magnifying-gallery" href="'.$live_site.'/'.$images[$c-1].'" style="background-image:url(\''.$live_site.'/'.$images[$c-1].'\'); "></a>
							</div>';
			if($c%6==0 || $c==$i){
				echo '	</div>';
			}
		}
		echo '		</div>
				</div>';
	}

	//get 9 similar actions
	date_default_timezone_set('Europe/Athens');

	$activityClass = new RemotedbActivity();

	$fields = ['a.id', 'a.origin', 'a.alias', 'a.image', 'a.best_practice', 'a.name', 'aa.address', 'aa.action_date_start', 'aa.action_date_end', 'a.team_id', 'a.accmr_team_id'];
	$where = "a.action_id=0 AND a.id!='".$action->id."' AND a.published=1 AND aa.action_date_end>='".date('Y-m-d H:i:s')."'";

	// if ($action->area > 0) {
	// 	$where = $where_initial . " AND aa.area='".$action->area."' ";
	// }
	$group_by = "a.id";
	$order_by = "aa.action_date_end DESC";
	$limit = 9;
	$all_actions = $activityClass->getActivitiesSubactivities($fields, $where, $order_by, $limit, $group_by);

	$all_actions1 = (array)$all_actions;
	shuffle($all_actions1);
	$all_actions = (object)$all_actions1;

	// NOTE: supposed to look for area and bring first the first 9 or less action in this area....but we do not know the action area, only the subactions one...
	// TODO: get subactions areas and make a query for all of them
	//$actions_left = 9 - count($other_actions);

	//$all_actions = $other_actions;

	// if ($actions_left > 0) {
	// 	$limit = $actions_left;
	// 	$other_actions1 = $activityClass->getActivitiesTeams($fields, $where_initial, $group_by, $order_by, $limit);

	// 	$all_actions1 = array_merge((array) $other_actions, (array) $other_actions1);
	// 	shuffle($all_actions1);
	// 	$all_actions = (object)$all_actions1;
	// } else {
	// 	$all_actions1 = (array)$all_actions;
	// 	shuffle($all_actions1);
	// 	$all_actions = (object)$all_actions1;
	// }
?>
   <h3 class="gallery-title"><?php echo JText::_('COM_ACTIONS_SEE_MORE'); ?></h3>
   <div class="module module--synathina more_actions">
      <div class="gallery gallery--singlerow gallery--filter" rel="js-start-gallery">
<?php
	$a=1;
	$f=1;
	foreach($all_actions as $all_action){
		if ($all_action->origin == 1) {
			$query = "SELECT t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
						WHERE t.id='".$all_action->team_id."' LIMIT 1";
			$db->setQuery($query);
			$team = $db->loadObject();
		} else {
			$query = "SELECT t.name AS tname, t.alias AS talias, t.logo AS tlogo FROM #__teams AS t
						WHERE t.id='".$all_action->accmr_team_id."' LIMIT 1";
			$db_remote->setQuery($query);
			$team = $db_remote->loadObject();
		}
		$link=JRoute::_('index.php?option=com_actions&view=action&id='.$all_action->id.':'.$all_action->alias.'&Itemid='.@$_REQUEST['Itemid']);
		if($all_action->image!=''){
			list($width, $height) = @getimagesize('images/actions/main_images/'.$all_action->image);
			//192 155
			$image_path='images/actions/main_images/'.$all_action->image;
			if($width>$height){
				//$max_width='width:192px;';
				$max_height='max-height:310px;';
				$bg_height='auto';
				$bg_width='100%';
			}else{
				$max_height='max-height:155px;';
				$max_width='max-width:392px;';
				$bg_width='auto';
				$bg_height='100%';
			}
		}else{
			$image_path='images/template/no-team.jpg';
		}
		echo '	<div class="gallery-item-2" style="position:relative">
						'.(@$all_action->best_practice==1?'<div class="badge-icon"><a href="'.$link.'"><img style="max-width:56px" src="images/template/best.png" alt="" /></a></div>':'').'
						<a href="'.$link.'" class="fill" style="background-color:#FFF; background-size: '.@$bg_width.' '.@$bg_height.'; background-position: center center;'.@$max_width.@$max_height.';background-image:url(\''.$image_path.'\')"></a>';
		$start_date=JHTML::_('date', $all_action->action_date_start, 'd-m-Y');
		$end_date=JHTML::_('date', $all_action->action_date_end, 'd-m-Y');
    	echo '  	<div class="caption">
						<a href="'.$link.'"><span class="caption-title">'.stripslashes($all_action->name).'</span></a>
							<span class="caption-details">'.$all_action->address.'</span>
							<span class="caption-details">'.($start_date!=$end_date?$start_date.' – '.$end_date:$start_date).'</span>
							<em class="caption-italic">'.JText::_('COM_ACTIONS_BY').' '.stripslashes($team->tname).'</em>
					</div>
				</div>';
		$a++;
	}
?>
      </div>
   </div>
</div>

<?php

function clean($string) {
   return str_replace('"', '', $string); // Replaces all quotes with nothing.

   //return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

//meta tags
$document->setMetaData( 'title', clean($action->name) );

$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'συνΑθηνά' );
$document->setMetaData( 'twitter:description', clean($action->name) );
$document->setMetaData( 'twitter:image', 'http://www.synathina.gr/images/actions/main_images/'.$action->image );

$document->setMetaData( 'og:url', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
$document->setMetaData( 'og:type', 'article' );
$document->setMetaData( 'og:title', clean($action->name) );
$document->setMetaData( 'og:description', clean(strip_tags($desc)) );
$document->setMetaData( 'og:image', 'http://www.synathina.gr/images/actions/main_images/'.$action->image );

$opengraph = '<meta property="og:url" content="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">' ."\n";
$opengraph .= '<meta property="og:type" content="article">' ."\n";
$opengraph .= '<meta property="og:title" content="'.clean($action->name).'">' ."\n";
$opengraph .= '<meta property="og:description" content="'.clean(strip_tags($desc)).'">' ."\n";
$opengraph .= '<meta property="og:image" content="http://www.synathina.gr/images/actions/main_images/'.$action->image.'">' ."\n";

$document->addCustomTag($opengraph);

?>
