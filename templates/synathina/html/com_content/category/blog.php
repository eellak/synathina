<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

//JHtml::_('behavior.caption');

$breadcumbs_modules = JModuleHelper::getModules('breadcumbs');
$article_modules = JModuleHelper::getModules('articles');

//connect to db
$db = JFactory::getDBO();

//get menu item notes
$note = '';
$query = "SELECT note FROM #__menu WHERE id='".@$_REQUEST['Itemid']."' ";
$db->setQuery($query);
$note = $db->loadResult();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuname = $menu->getActive()->title;

function youtubeID($url)
{
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
}

if ($note == 'synathina') {
/*---------SYNATHINA TEMPLATE--------*/
?>
<div class="l-synathina">
<?php
	$s=1;
	$c=count($this->lead_items);
	foreach ($this->lead_items as $key => &$item) :
		$this->item = & $item;
		$item->counter = $s;
		$this->item = & $item;
		$item->total = $c;
		$this->item = & $item;
		echo $this->loadTemplate('item');
		$s++;
	endforeach;
?>
</div>

<?php
/*-----END OF SYNATHINA TEMPLATE-----*/
}elseif($note=='teams' || $note=='supporters'){
/*---------TEAMS TEMPLATE--------*/
	$team_modules=JModuleHelper::getModules('teams');
	echo '<div class="l-teams l-teams--listing">
					<div class="module module--synathina">
						<div class="module-skewed">';
	foreach ($this->lead_items as $key => &$item) :
		$this->item = & $item;
		echo $this->loadTemplate('item');
	endforeach;
	echo '		</div>
					</div>';
	if (count($team_modules)>0) :
		foreach ($team_modules as $team_module){
			echo JModuleHelper::renderModule($team_module);
		}
	endif;
	echo '</div>';
/*-----END OF TEAMS TEMPLATE-----*/
/*-----START OF EU TEMPLATE-----*/
}elseif($note=='eu'){
?>
<div class="l-synathina">
<?php
	foreach ($this->lead_items as $key => &$item) :
		$this->item = & $item;
		echo $this->loadTemplate('item');
	endforeach;
?>
</div>
<?php
/*-----END OF EU TEMPLATE-----*/
}elseif($note=='opencalls1'){

?>
<div class="l-news l-news--list opencalls">
<?php
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
?>
	<div class="thumbnail-list">
		<h2 class="thumbnail-list__title"><?php echo $this->category->title; ?></h2>
<?php
	$i=1;
	foreach ($this->lead_items as $key => &$item) :
		//print_r($item);
		if($i<9){
			$this->item = & $item;
			echo $this->loadTemplate('item');
			if($i%2==0){
				echo '<div class="articles_by_two">&nbsp;</div>';
			}
			$i++;
		}
	endforeach;

?>
	</div>
	<div class="more-articles"></div>
	<button class="load-more-btn more-articles-button" rel="js-load-more-articles"></button>
	<input type="hidden" class="articles_counter" name="articles_counter" value="8" />
	<input type="hidden" class="article_url_parameter" name="article_url_parameter" value="" />
	<input type="hidden" class="articles_itemid" name="articles_itemid" value="<?php echo @$_REQUEST['Itemid']; ?>" />
	<input type="hidden" class="all_articles_counter" name="all_articles_counter" value="<?php echo count($this->lead_items); ?>" />
	<input type="hidden" class="param_catid" name="param_catid" value="<?php echo $this->category->id; ?>" />
	<div class="latest-articles odd-even">

	</div>
</div>
<?php
} else {

?>
<div class="l-draseis">
<?php
	$top_text_modules = JModuleHelper::getModules('top_text');
	if (count($top_text_modules)) :
		foreach ($top_text_modules as $top_text_module) {
			echo '	<div class="module module--synathina" style="margin-bottom: 70px;">
      					<div class="module-skewed module-skewed--gray">
         					<div class="module-wrapper">
								<h3 class="module-title">'.$top_text_module->title.'</h3>
								'.$top_text_module->content.'
							</div>
						</div>
					</div>';
		}
	else:
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
	endif;
?>

	<div class="filter-results">
<?php if (!count($top_text_modules)) { ?>
		<h2 class="thumbnail-list__title list-title"><?php echo $this->category->title; ?></h2>
<?php } ?>
<?php
	$c = 1;
	foreach ($this->intro_items as $key => &$item) :
		$this->item = & $item;
		$this->item->counter = $c;
		echo $this->loadTemplate('item');
		$c++;
	endforeach;
?>
	</div>
	<?php echo $this->pagination->getPagesLinks(); ?>
<?php
	if (count($article_modules)>0) :
		foreach ($article_modules as $article_module){
			echo JModuleHelper::renderModule($article_module);
		}
	endif;
?>
</div>
<?php
}

//meta tags
$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';
$document = JFactory::getDocument();
$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'ÏƒÏ…Î½Î‘Î¸Î·Î½Î¬' );
$document->setMetaData( 'twitter:description', $menuname );
$document->setMetaData( 'twitter:image', $article_image );
?>