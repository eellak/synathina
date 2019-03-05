<?php
defined('_JEXEC') or die;


function youtubeID($url)
{
 $res = explode('v=', $url);
 if (isset($res[1])) {
	$res1 = explode('&', $res[1]);
	if (isset($res1[1])) {
		$res[1] = $res1[0];
	}
	$res1 = explode('#',$res[1]);
	if (isset($res1[1])) {
		$res[1]  = $res1[0];
	}
 }

 return substr($res[1], 0, 12);
}

//db connection
$db = JFactory::getDBO();

//get menu item notes
$query = "SELECT note FROM #__menu WHERE id='".@$_REQUEST['Itemid']."' ";
$db->setQuery($query);
$note = $db->loadResult();

$doc = JFactory::getDocument();
$lang_code_array=explode('-',$doc->language);
$lang_code=$lang_code_array[0];

date_default_timezone_set('Europe/Athens');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$attribs  = json_decode($this->item->attribs);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);

function teams_count2($year)
{
	if (!$year) {
		$year = date('Y');
	}
	$db = JFactory::getDBO();
	$query = "SELECT COUNT(u.id)	FROM #__users AS u
				INNER JOIN #__teams AS t
				ON t.user_id=u.id
				WHERE u.block=0 AND u.activation='' AND t.hidden=0 AND t.published=1 AND t.created>='".$year."-01-01 00:00:00' AND t.created<='".$year."-31-21 23:59:59'  ";
	$db->setQuery($query);
	$db->execute();

	return $db->loadResult();
}

function donators_count()
{
	$db = JFactory::getDBO();
	$query = "SELECT COUNT(u.id)	FROM #__users AS u
				INNER JOIN #__teams AS t
				ON t.user_id=u.id
				WHERE u.block=0 AND u.activation='' AND t.published=1 AND t.support_actions=1 ";
	$db->setQuery($query);
	$db->execute();

	return $db->loadResult();
}

function count_actions_1($year)
{
	$where = "aa.published=1 AND a.published=1 AND aa.action_id>0 AND aa.action_date_start>='".$year."-01-01 00:00:00'";
	if ($year == date('Y')) {
		$where .= " AND aa.action_date_start<='".date('Y-m-d H:i:s')."' ";
	} else {
		$where .= " AND aa.action_date_start<='".$year."-31-21 23:59:59' ";
	}
	//remote db
	$activityClass = new RemotedbActivity();
	$activities_count = $activityClass->getActivitiesCount($where);

	return $activities_count;
}

$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');
$stegi_modules=JModuleHelper::getModules('stegi');

$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';

if ($note == 'stegi') {
	//get di images
	$query = "SELECT * FROM #__di_images WHERE object_id='".$this->item->id."' ORDER BY ordering ASC";
	$db->setQuery($query);
	$imgs = $db->loadObjectList();
?>
<div class="l-stegh">
	<div class="module module--synathina">
		<div class="module-skewed module-skewed--gray">
			<div class="module-wrapper">
				<h3 class="module-title"><?php echo $this->escape($this->item->title); ?></h3>
				<?php echo $this->item->introtext; ?>
			</div>
		</div>
	</div>
<?php
foreach ($stegi_modules as $stegi_module) {
	echo JModuleHelper::renderModule($stegi_module);
}
if (count($imgs)>0) {
?>
	<h3 class="gallery-title">Gallery</h3>
	<div class="module module--synathina more_actions">
		<div class="gallery gallery--singlerow gallery--filter" rel="js-start-gallery">
<?php
	$c = 1;
	$p = 1;
	foreach ($imgs as $img) {
		$image = 'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename;
		echo '	<div class="gallery-item" style="position:relative">
						<a class="fill" class="magnifying-gallery" href="'.$image.'" style="background-image:url(\''.$image.'\'); "></a>
				</div>';
		$c++;
	}
?>
		</div>
	</div>
<?php } ?>
</div>

<?php } else { ?>

<div class="l-news--article">
<?php
	foreach ($breadcumbs_modules as $breadcumbs_module) {
		echo JModuleHelper::renderModule($breadcumbs_module);
	}
?>
	<article class="c-article">
<?php
		$top_text_modules = JModuleHelper::getModules('top_text');
		if (count($top_text_modules)) :
			//get action by year
			$slider_1_2013 = 185 + 23;
			$slider_1_2014 = 317 + 70;
			$slider_1_2015 = 451 + 169;
			$slider_1_2016 = 638 + 459;
			for ($y=2017; $y<=date('Y'); $y++) {
				${'slider_1_'.$y} = count_actions_1($y);
			}
			//total
			$slider_1_all = 0;
			for ($y=2013; $y<=date('Y'); $y++) {
				$slider_1_all += ${'slider_1_'.$y};
			}
			$slider_2_2013 = 42;
			$slider_2_2014 = 77;
			$slider_2_2015 = 75;
			$slider_2_2016 = 87;
			for ($i=2017; $i<=date('Y'); $i++) {
				${'slider_2_'.$i} = teams_count2($i);
			}
			//total teams
			$slider_2_all_teams = 0;
			for ($y=2013; $y<=date('Y'); $y++) {
				$slider_2_all_teams += ${'slider_2_'.$y};
			}

			$total_donators = donators_count();
			$replace_array = [$slider_1_all,$slider_2_all_teams,$total_donators];
			$replace_array1 = ['{total_actions}','{total_teams}','{total_donators}'];
			foreach ($top_text_modules as $top_text_module) {
				echo '	<div class="module module--synathina" style="margin-bottom: 70px;">
	      					<div class="module-skewed module-skewed--gray">
	         					<div class="module-wrapper">
									<h3 class="module-title">'.$top_text_module->title.'</h3>
									' . str_replace($replace_array1, $replace_array, $top_text_module->content) . '
								</div>
							</div>
						</div>';
			}
		else:
			echo '<h1>' . $this->escape($this->item->title) . '</h1>';
		endif;
		echo '<figure role="complementary">';
		if ($note == 'opencalls') {
			//activities
			$query="SELECT a.*
							FROM #__team_activities AS a
							WHERE a.published=1 ";
			$db->setQuery( $query );
			$activities = $db->loadObjectList();
			$activities_array_info=array();
			foreach($activities as $activity){
				$activities_array_info[$activity->id]=array($activity->name, $activity->image);
			}
			//team info
			$query = "SELECT id, name FROM #__teams
						WHERE user_id='".$this->item->created_by."' LIMIT 1";
			$db->setQuery($query);
			$team = $db->loadObject();

			if ($team) {
				echo '<h2 style="margin:0px; padding:0px"><a style="color:#05c0de" href="'.JRoute::_('index.php?option=com_teams&view=team&id='.$team->id.'&Itemid=140').'">'.$team->name.'</a></h2>';
			}
			echo '<time class="opencall_time">Deadline: '.(trim(@$attribs->opencall_date)!=''?' '.@JHTML::_('date', @$attribs->opencall_date, 'd/m/Y'):'').'</time>';
			$activities_array=@$attribs->opencall_activities;
			//$activities_array=explode(',',$attribs->opencall_activities);
			if (count($activities_array)>0) {
				echo '<div class="opencall_activities"><ul class="inline-list inline-list--separated thematiki--list">';
			}
			for ($i=0; $i<count($activities_array); $i++) {
				if ($activities_array[$i] != '') {
					echo '<div class="thematiki thematiki--perivallon">
									<img src="'.$activities_array_info[$activities_array[$i]][1].'" width="45" height="35" alt="'.$activities_array_info[$activities_array[$i]][0].'" title="'.$activities_array_info[$activities_array[$i]][0].'" />
								</div>';
				}
			}
			if (count($activities_array) > 0) {
				echo '</ul></div>';
			}
		}
		//get di images
		$query = "SELECT * FROM #__di_images WHERE object_id='".$this->item->id."' ORDER BY ordering ASC";
		$db->setQuery($query);
		$imgs = $db->loadObjectList();
		if (count($imgs)>0) {
			foreach ($imgs as $img) {
				if ($img->link != '') {
					echo '<a href="javascript:void(null)" class="figure-item video-container">
									<div class="youtube" id="'.youtubeID($img->link).'" data-params="modestbranding=1&showinfo=0&controls=1&vq=hd720"></div>
								</a>';
				} else {
					echo '<a href="'.JURI::base().'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename.'" class="figure-item magnifying-gallery">
									<img src="'.JURI::base().'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename.'" alt="" />
									<p>'.$img->description.'</p><br />
								</a>';
				}
			}
		}
		echo '<br /></figure>';
	if ($note == 'press' || $note == 'stats') {
		$new_text = $this->item->text;
		echo $this->item->event->afterDisplayContent;
	} else {
    	if ($note != '') {
    		if ($this->item->fulltext == '') {
				$new_text = preg_replace('#\s*\[caption[^]]*\].*?\[/caption\]\s*#is', '', $this->item->introtext);
			} else {
				$new_text = preg_replace('#\s*\[caption[^]]*\].*?\[/caption\]\s*#is', '', $this->item->fulltext);
			}
		} else {
			if ($this->item->fulltext == '') {
				$new_text = strip_tags($this->item->introtext,'<strong><a><br><br /><br/><p><img><ul><li><i><u><em>');
			} else {
				$new_text = strip_tags($this->item->fulltext,'<strong><a><br><br /><br/><p><img><ul><li><i><u><em>');
			}
			//$new_text = $this->item->text;
		}
	}
?>
		<div role="main" class="c-article__content">
<?php
	$imgs_tags = $imgs;
?>
<?php echo $new_text; ?>

<?php
	//attachments
	$query = "SELECT * FROM #__attachments WHERE state=1 AND parent_type='com_content' AND parent_entity='article' AND parent_id='".$this->item->id."' ORDER BY id DESC";
	$db->setQuery($query);
	$atts = $db->loadObjectList();
	if (count($atts) > 0) {
	echo '<div class="document-download">
					<h3 class="text-center">'.($lang_code=='en'?'Downloads':'Αρχεία').'</h3>
					<ul class="inline-list">';
	foreach ($atts as $att) {
		echo '	<li>
							<a href="'.$att->url.'" download="'.addslashes($att->filename).'" target="_blank">
								<i class="doc-icon doc-icon--pdf"></i>
								<div>'.$att->filename.'</div>
							</a>
						</li>';
	}
	echo '	</ul>
				</div>';
	}
	//meta tags
	if (count(@$imgs_tags) > 0) {
		$i = 0;
		foreach ($imgs_tags as $img) {
			if ($i == 0) {
				$article_image = 'http://www.synathina.gr/images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename;
			}
			$i++;
		}
	}
?>
		<div class="pull-right share-icons">
			<a href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $this->item->title;?>&amp;p[summary]=<?php echo addslashes(strip_tags($note=='stegi'?$this->item->introtext:$new_text));?>&amp;p[url]=<?php echo JUri::current();?>&amp;p[images][0]=<?php echo $article_image; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600'); return false;">
				<img src="<?php echo JURI::base(); ?>images/template/facebook.png" alt="facebook" />
			</a>&nbsp;
			<a href="http://twitter.com/home?status=<?php echo $this->item->title; ?> <?php echo urlencode(JUri::current());?>"  onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600');return false;">
				<img src="<?php echo JURI::base(); ?>images/template/tweet.png" alt="twitter" />
			</a>
		</div>
		<div class="clearfix"></div>
	</article>
</div>
<?php
}
//meta tags
$doc->setMetaData( 'twitter:card', 'summary_large_image' );
$doc->setMetaData( 'twitter:site', '@synathina' );
$doc->setMetaData( 'twitter:title', 'συνΑθηνά' );
$doc->setMetaData( 'twitter:description', $this->escape($this->item->title) );
$doc->setMetaData( 'twitter:image', $article_image );

$doc->setMetaData( 'og:url', JUri::current() );
$doc->setMetaData( 'og:type', 'article' );
$doc->setMetaData( 'og:title', $this->escape($this->item->title) );
$doc->setMetaData( 'og:description', addslashes(strip_tags($note=='stegi'?$this->item->introtext:$new_text)) );
$doc->setMetaData( 'og:image', $article_image );
?>

