<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
$team_files = $this->team_files;

$document = JFactory::getDocument();
$document->setTitle($team->name);

function strip_single_tag($str,$tag){

    $str1=preg_replace('/<\/'.$tag.'>/i', '', $str);

    if($str1 != $str){

        $str=preg_replace('/<'.$tag.'[^>]*>/i', '', $str1);
    }

    return $str;
}

//db connection
$db = JFactory::getDBO();

$user = JFactory::getUser();

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

$months=array(1=>'ΙΑΝ','ΦΕΒ','ΜΑΡ','ΑΠΡ','ΜΑΙ','ΙΟΥΝ','ΙΟΥΛ','ΑΥΓ','ΣΕΠ','ΟΚΤ','ΝΟΕ','ΔΕΚ');

$config= new JConfig();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

//get lang variables
$lang = JFactory::getLanguage();
$this->language = $lang->getTag();//$doc->language;
$lang_code_array=explode('-',$this->language);
$lang_code=$lang_code_array[0];

//meta tags
$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';
if($team->logo!=''){
	$article_image='http://www.synathina.gr/'.$team->logo;
}
$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'συνΑθηνά' );
$document->setMetaData( 'twitter:description', $team->name );
$document->setMetaData( 'twitter:image', $article_image );

$document->setMetaData( 'og:url', JUri::current() );
$document->setMetaData( 'og:type', 'article' );
$document->setMetaData( 'og:title', $team->name );
$document->setMetaData( 'og:description', strip_tags($team->description) );
$document->setMetaData( 'og:image', $article_image );



if(!$team || $team->hidden==1){
	header('Location: '.JRoute::_('index.php?option=com_content&view=category&layout=blog&id='.($lang_code=='en'?'16':'12').'&Itemid='.($lang_code=='en'?'147':'137')));
	exit();
}
//$subteams=$this->subteams;
//print_r($team);
?>
<div class="l-teams l-teams--article">
   <div class="module module--synathina">
      <div class="module-skewed">
         <div class="media">
            <div class="media-left text-center">
<?php
	if($team->logo!=''){
?>
               <a href="">
                  <img src="<?php echo $team->logo; ?>" style="max-width:200px" alt="">
               </a>
<?php
	}
?>
               <ul class="inline-list social-icons">
<?php
	if($team->fb_link!=''){
		$fb_link1=$team->fb_link;
		$prefix=substr($team->fb_link,0,4);
		if($prefix!='http'){
			$fb_link1='http://'.$team->fb_link;
		}
		echo '<a href="'.$fb_link1.'" target="_blank"><span class="fa fa-facebook"></span></a>';
	}
	if($team->tw_link!=''){
		$tw_link1=$team->tw_link;
		$prefix=substr($team->tw_link,0,4);
		if($prefix!='http'){
			$tw_link1='http://'.$team->tw_link;
		}
		echo '<a href="'.$tw_link1.'" target="_blank"><span class="fa fa-twitter"></span></a>';
	}
	if($team->pn_link!=''){
		$pn_link1=$team->pn_link;
		$prefix=substr($team->pn_link,0,4);
		if($prefix!='http'){
			$pn_link1='http://'.$team->pn_link;
		}
		echo '<a href="'.$pn_link1.'" target="_blank"><span class="fa fa-pinterest"></span></a>';
	}
	if($team->in_link!=''){
		$in_link1=$team->in_link;
		$prefix=substr($team->in_link,0,4);
		if($prefix!='http'){
			$in_link1='http://'.$team->in_link;
		}
		echo '<a href="'.$in_link1.'" target="_blank"><span class="fa fa-instagram"></span></a>';
	}
	if($team->li_link!=''){
		$li_link1=$team->li_link;
		$prefix=substr($team->li_link,0,4);
		if($prefix!='http'){
			$li_link1='http://'.$team->li_link;
		}
		echo '<a href="'.$li_link1.'" target="_blank"><span class="fa fa-linkedin"></span></a>';
	}
	if($team->go_link!=''){
		$go_link1=$team->go_link;
		$prefix=substr($team->go_link,0,4);
		if($prefix!='http'){
			$go_link1='http://'.$team->go_link;
		}
		echo '<a href="'.$go_link1.'" target="_blank"><span class="fa fa-google-plus"></span></a>';
	}
	if($team->yt_link!=''){
		$yt_link1=$team->yt_link;
		$prefix=substr($team->yt_link,0,4);
		if($prefix!='http'){
			$yt_link1='http://'.$team->yt_link;
		}
		echo '<a href="'.$yt_link1.'" target="_blank"><span class="fa fa-youtube"></span></a>';
	}
?>
               </ul>
               <a href="mailto:<?php echo $team->contact_1_email; ?>" class="mail-to" target="_top">Επικοινωνία</a>
            </div>
            <div class="media-body">
<?php
	if($user->id==$team->user_id){
?>
               <div class="edit-zone">
                  <a href="index.php?option=com_users&view=profile" class="pull-right"><i class="fa fa-pencil"></i></a>
               </div>
<?php
	}
?>
<?php
	if($team->web_link!=''){
		$weblink1=$team->web_link;
		$prefix=substr($team->web_link,0,4);
		if($prefix!='http'){
			$weblink1='http://'.$team->web_link;
		}
?>
               <dl class="dl-horizontal">
                   <dt>Website</dt>
                   <dd><a target="_blank" href="<?php echo $weblink1; ?>"><?php echo $team->web_link; ?></a></dd>
               </dl>
<?php
	}
	foreach ($team_files as $team_file) {
		$file_array = (explode('.', $team_file->path));
		if ($file_array) {
?>
               <dl class="dl-horizontal">
                  <dt><a href="<?php echo 'images/team_files/'.$team->id.'/'.$team->id.'.'.end($file_array); ?>" target="_blank">Παρουσίαση ομάδας</a></dt>
               </dl>
<?php
		}
	}
?>
               <dl class="dl-horizontal">
                  <dt>Θεματικές Δραστηριοποίησης</dt>
                  <dd>
                     <ul class="inline-list inline-list--separated thematiki--list">
<?php
	$query="SELECT a.name, a.image
					FROM #__team_activities AS a
					WHERE FIND_IN_SET(id, '".$team->activities."') AND a.published=1 ";
	$db->setQuery( $query );
	$activities = $db->loadObjectList();
	foreach($activities as $activity){
		echo '<span class="thematiki thematiki--perivallon">
						 <img src="'.$activity->image.'" width="45" height="35" alt="" title="'.$activity->name.'" />
					</span>';
	}
?>
                     </ul>
                  </dd>
               </dl>
               <h4 class="media-heading">
                 <?php echo $team->name; ?>
               </h4>
               <?php echo $team->description; ?>
				<div class="pull-right share-icons">
					<a href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $team->name;?>&amp;p[summary]=<?php echo addslashes(strip_tags($team->description));?>&amp;p[url]=<?php echo JUri::current();?>&amp;p[images][0]=<?php echo $article_image; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600'); return false;">
						<img src="<?php echo JURI::base(); ?>images/template/facebook.png" alt="facebook" />
					</a>&nbsp;
					<a href="http://twitter.com/home?status=<?php echo $team->name; ?> <?php echo urlencode(JUri::current());?>"  onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=400,width=600');return false;">
						<img src="<?php echo JURI::base(); ?>images/template/tweet.png" alt="twitter" />
					</a>
				</div>
            </div>
         </div>
      </div>
   </div>
<?php
	$i=0;
	$images=array();
	foreach (glob('images/team_photos/'.$team->id.'/*.*') as $filename) {
		$images[]=$filename;
		$i++;
	}
	//echo $i;
	$p=1;
	if($i>0){
		echo '<div class="module module--synathina no-margin">
						<h3 class="gallery-title">Gallery</h3>
						<div class="gallery gallery--multirow" rel="js-start-gallery">';
		for($c=1; $c<=$i; $c++){
			if($c==1 || ($c-1)%6==0){
				echo '<div class="gallery-frame" data-id="'.$p.'">';
				$p++;
			}
			echo '<div class="gallery-item">
							<a class="fill" class="magnifying-gallery" href="'.$images[$c-1].'" style="background-image:url(\''.$images[$c-1].'\'); "></a>
						</div>';
			if($c%6==0 || $c==$i){
				echo '</div>';
			}
		}
		echo '	</div>
					</div>';
	}

?>
<?php
	$i=1;
	$actions = $this->activities_support;
	if(count($actions)>0){
		echo '<div class="module module--synathina no-margin">
						<h3 class="gallery-title">Δράσεις που υποστηρίξαμε</h3>
						<div class="gallery gallery--thumbnail" rel="js-start-gallery">';
		foreach($actions as $action){
			$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->id.':'.$action->alias.'&Itemid=138');
			echo '	<div class="gallery-item-1" data-id="'.$i.'">
								<article class="thumbnail">
									 <a href="'.$link.'" class="fill" style="background-image:url(images/actions/main_images/'.$action->image.')" ></a>
									 <div class="caption">
											<p>
												 '.$action->name.'
											</p>
									 </div>
								</article>
							</div>';
			$i++;
		}
		echo '	</div>
					</div>';
	}
?>
<?php
	$i=1;
	$actions = $this->activities_team;
	if(count($actions)>0){
		echo '<div class="module module--synathina">
						<h3 class="gallery-title">Δράσεις που οργανώσαμε</h3>
						<div class="gallery gallery--thumbnail" rel="js-start-gallery">';
		foreach($actions as $action){
			$link=JRoute::_('index.php?option=com_actions&view=action&id='.$action->id.':'.$action->alias.'&Itemid=138');
			echo '	<div class="gallery-item-1" data-id="'.$i.'">
								<article class="thumbnail">
									 <a href="'.$link.'" class="fill" style="background-image:url(images/actions/main_images/'.$action->image.')" ></a>
									 <div class="caption">
											<p>
												 '.$action->name.'
											</p>
									 </div>
								</article>
							</div>';
			$i++;
		}
		echo '	</div>
					</div>';
	}
?>
</div>
