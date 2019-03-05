<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Core');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

//connect to db
$db = JFactory::getDBO();
$itemid=@$_REQUEST['itemid'];
//get menu item notes
$note='';
$query = "SELECT note FROM #__menu WHERE id='".@$itemid."' ";
$db->setQuery($query);
$note = $db->loadResult();


$lang = JFactory::getLanguage();

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

$extension = 'com_content';
$base_dir = JPATH_SITE;
$language_tag = @$_REQUEST['lang'];
$language_array=explode('-',$language_tag);
$language_tag=$language_array[0].'-'.strtoupper($language_array[1]);
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
//echo $language_tag;

//get more articles
if($note=='opencalls'){
	$query="SELECT * FROM #__content WHERE ".($isroot==1?'':'state=1 AND')." ".($isroot==1?'':'publish_up<\''.date('Y-m-d H:i:s').'\' AND')." catid='".@$_REQUEST['catid']."' ORDER BY created DESC LIMIT ".@$_REQUEST['counter'].",8 ";

	$db->setQuery($query);
	$articles = $db->loadObjectList();
	/*$new_Lead_items = new ArrayObject();
	foreach ($articles as $key => &$item) :
		$attribs  = json_decode($item->attribs);
		$item->opencall_date=$attribs->opencall_date;
		$new_Lead_items->append($item);
	endforeach;
	$new_Lead_items1 = $new_Lead_items->getArrayCopy();
	//print_r($new_Lead_items1);
	usort($new_Lead_items1, function($a, $b)
	{
			return strcmp($b->opencall_date, $a->opencall_date);
	});
	$articles = new ArrayObject();
	$i=0;
	//echo '<div style="display:none">'.print_r($new_Lead_items1).'</div>';
	foreach ($new_Lead_items1 as $key => &$item) :
		if($i>@$_REQUEST['counter'] && $i<(@$_REQUEST['counter']+9)){
			$articles->append($item);
		}
		$i++;
	endforeach;*/

}else{
	$query="SELECT * FROM #__content WHERE ".($isroot==1?'':'state=1 AND')." ".($isroot==1?'':'publish_up<\''.date('Y-m-d H:i:s').'\' AND')." catid='".@$_REQUEST['catid']."' ORDER BY created DESC LIMIT ".@$_REQUEST['counter'].",4 ";
	$db->setQuery($query);
	$articles = $db->loadObjectList();
}
function youtubeID($url){
 $res = explode("v=",$url);
 if(isset($res[1])) {
	$res1 = explode('&',$res[1]);
	if(isset($res1[1])){
		$res[1] = $res1[0];
	}
	$res1 = explode('#',$res[1]);
	if(isset($res1[1])){
		$res[1] = $res1[0];
	}
 }
 return substr($res[1],0,12);
	 return false;
}

$a=1;
$i=0;
require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
echo '<div class="more--articles more--articles-hidden"><div class="thumbnail-list">';
	$extralink=@$_REQUEST['extralink'];

	foreach($articles as $article){
		//$link = JRoute::_('index.php?option=com_content&view=article&id='.$article->id).'&Itemid='.$itemid.$extralink;
		$article->slug    = $article->id . ':' . $article->alias;
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language).'&Itemid='.$itemid.$extralink);
		//echo 'index.php?option=com_content&view=article&id='.$article->id.'&Itemid='.$itemid.$extralink;
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
				//style="height:'.($height<310?$height.'px; margin-bottom:'.ceil(310-$height).'px':'310px').'"
				echo '<a href="'.$link.'"><img src="'.JURI::root( true ).'/images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename.'" class="img-responsive"  /></a>';
				if(count($imgs)==0){
					echo '<a href="'.$link.'"><img src="http://placehold.it/511x310" class="img-responsive" style="height:310px" /></a>';
				}
			}
		}
		$i++;
	}

		/*if($attribs1->article_video!=''){
			echo '<div class="module-embed">
							<iframe width="560" height="315" src="https://www.youtube.com/embed/'.youtubeID($attribs1->article_video).'" frameborder="0" allowfullscreen></iframe>
						</div>';
		}else{
			//get di images
			$query = "SELECT * FROM #__di_images WHERE object_id='".$article->id."' ORDER BY ordering ASC";
			$db->setQuery($query);
			$imgs = $db->loadObjectList();
			$i=0;
			foreach($imgs as $img){
				if($i==0){
					list($width, $height) = getimagesize(JUri::base().'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename);
					echo '<a href="'.$link.'"><img src="'.JUri::base().'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename.'" class="img-responsive" style="height:'.($height<310?$height.'px; margin:'.ceil((310-$height)/2).'px 0px':'310px').'" /></a>';
					if(count($imgs)==0){
						echo '<a href="'.$link.'"><img src="http://placehold.it/511x310" class="img-responsive" style="height:310px" /></a>';
					}
				}
				$i++;
			}
		}*/
?>
				<div class="caption">
					 <h3><a style="color:#5d5d5d" href="<?php echo $link; ?>"><?php echo $article->title; ?></a><?=($isroot==1&&$article->state==0?' <span style="color:red;">ανενεργό</span>':'')?></h3>
					 <time><?=($note=='opencalls'?'Deadline: ':'')?><?php echo JHTML::_('date', ($note=='opencalls'?@$attribs1->opencall_date:$article->created), 'd/m/Y');?></time>
						<p style="max-height:208px; overflow:hidden;"><?php echo strip_tags(($article->introtext==''?$article->fulltext:$article->introtext).'<strong><a>'); ?></p>
						<a class="caption-more" href="<?php echo $link; ?>"><?php echo JText::_('COM_CONTENT_FEED_READMORE'); ?></a>
				</div>
		 </article>
	</div>
<?php
		if($a%2==0){
			echo '<div class="articles_by_two">&nbsp;</div>';
		}
		$a++;
	}
echo '</div></div>';
?>