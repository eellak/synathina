<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$config= new JConfig();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();

//connect to db
$db = JFactory::getDBO();
$params1  = json_decode($module->params);


$query="SELECT * FROM #__content WHERE state=1 AND catid='".$params1->catid."' AND publish_up<'".date('Y-m-d H:i:s')."' ORDER BY created DESC LIMIT 4 ";
$db->setQuery($query);
$articles = $db->loadObjectList();

$query="SELECT COUNT(id) FROM #__content WHERE state=1 AND catid='".$params1->catid."' AND publish_up<'".date('Y-m-d H:i:s')."' ";
$db->setQuery($query);
$articles_all = $db->loadResult();

?>

<div class="thumbnail-list articles-view">
	<h2 class="thumbnail-list__title"><?php echo $module->title; ?></h2>
<?php	$a=1;
	foreach($articles as $article){
		$article->slug    = $article->id . ':' . $article->alias;
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language).'&art=1');
		$attribs1  = json_decode($article->attribs);
?>
	<div class="thumbnail-list__item">
		 <article class="thumbnail">
<?php
	//get di images
	$query = "SELECT * FROM #__di_images WHERE object_id='".$article->id."' ORDER BY ordering ASC LIMIT 1";
	$db->setQuery($query);
	$imgs = $db->loadObjectList();
	$i=0;
	foreach($imgs as $img){
		if($i==0){
			if($img->link!=''){
				echo '<div class="module-embed">
								<iframe width="560" height="315" src="https://www.youtube.com/embed/'.youtubeID($img->link).'" frameborder="0" allowfullscreen></iframe>
							</div>';
			}else{
				list($width, $height) = @getimagesize('images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename);
				echo '<a href="'.$link.'"><img src="images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename.'" class="img-responsive" style="" /></a>';
				if(count($imgs)==0){
					echo '<a href="'.$link.'"><img src="http://placehold.it/511x310" class="img-responsive" style="height:310px" /></a>';
				}
			}
		}
		$i++;
	}
	if($article->introtext==''){
		$text=strip_tags($article->fulltext,'<strong><a>');
	}else{
		$text=strip_tags($article->introtext,'<strong><a>');
	}
	$new_text=preg_replace('#\s*\[caption[^]]*\].*?\[/caption\]\s*#is', '', $text);
?>
				<div class="caption">
					 <h3><a style="color:#5d5d5d" href="<?php echo $link; ?>"><?php echo $article->title; ?></a></h3>
					 <time><?php echo JHTML::_('date', $article->created, 'd/m/Y');?></time>
					 <p>
						<p style="max-height:220px; overflow:hidden;"><?php echo $new_text; ?></p>
						<a class="caption-more" href="<?php echo $link; ?>"><?php echo JText::_('COM_CONTENT_FEED_READMORE'); ?></a>
					 </p>
				</div>
		 </article>
	</div>
<?php 			if($a%2==0){				echo '<div class="articles_by_two">&nbsp;</div>';			}			$a++;}?>
</div>
<div class="more-articles"></div>
<button class="load-more-btn more-articles-button" rel="js-load-more-articles"></button>
<input type="hidden" class="articles_counter" name="articles_counter" value="4" />
<input type="hidden" class="article_url_parameter" name="article_url_parameter" value="&art=1" />
<input type="hidden" class="articles_itemid" name="articles_itemid" value="<?php echo @$_REQUEST['Itemid']; ?>" />
<input type="hidden" class="all_articles_counter" name="all_articles_counter" value="<?php echo $articles_all; ?>" />
<input type="hidden" class="param_catid" name="param_catid" value="<?php echo $params1->catid; ?>" />





