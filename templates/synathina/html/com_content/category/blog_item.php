<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//connect to db
$db = JFactory::getDBO();

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

//get menu item notes
$note='';
$query = "SELECT note FROM #__menu WHERE id='".@$_REQUEST['Itemid']."' ";
$db->setQuery($query);
$note = $db->loadResult();

$slider_modules=JModuleHelper::getModules('slider');

// Create a shortcut for params.
$params = $this->item->params;
$images  = json_decode($this->item->images);
$attribs  = json_decode($this->item->attribs);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);
//print_r($attribs);
if ($params->get('access-view')) :
	$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
else :
	$menu = JFactory::getApplication()->getMenu();
	$active = $menu->getActive();
	$itemId = $active->id;
	$link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
	$link->setVar('return', base64_encode(JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language), false)));
endif;
if($note=='synathina'){
/*---------SYNATHINA TEMPLATE--------*/
?>
<div class="module module--synathina">
	<div class="module-skewed">
		 <div class="module-wrapper">
				<h3 class="module-title"><?php echo $this->item->title; ?></h3>
	<?php echo $this->item->introtext ?>
		 </div>
<?php
	if($this->item->counter==$this->item->total){
		if (count($slider_modules)>0) :
			foreach ($slider_modules as $slider_module){
				echo JModuleHelper::renderModule($slider_module);
			}
		endif;
	}
?>
	</div>
</div>

<?php
/*-----END OF SYNATHINA TEMPLATE-----*/
} elseif ($note == 'teams' || $note == 'supporters') {
/*---------TEAMS TEMPLATE--------*/
	echo '<div class="module-wrapper">
					<h3 class="module-title">'.$this->item->title.'</h3>
					'.$this->item->introtext.'
        </div>	';
/*-----END OF TEAMS TEMPLATE-----*/
/*-----START OF EU TEMPLATE-----*/
}elseif($note=='eu'){
?>
	<div class="module module--synathina">
			<div class="module-skewed">
				 <div class="module-wrapper">
						<h3 class="module-title"><?php echo $this->item->title; ?></h3>
						<?php echo str_replace('<img ','<img class="img-responsive" ',$this->item->introtext); ?>
				 </div>
			</div>
	 </div>
<?php
/*-----END OF EU TEMPLATE-----*/
} else {
	//get di images
	$query = "SELECT * FROM #__di_images WHERE object_id='".$this->item->id."' ORDER BY ordering ASC LIMIT 1";
	$db->setQuery($query);
	$imgs = $db->loadObjectList();
?>
	<div class="media">
		<div class="media-left badge-item" >
<?php foreach ($imgs as $img) { ?>
			<div class="badge-image">
				<a href="<?php echo $link; ?>">
					<img class="results-img-width" src="<?php echo JURI::base().'images/di/'.$img->object_id.'_'.$img->object_image_id.'_'.$img->filename; ?>" alt="<?php echo $this->item->title; ?>" />
				</a>
			</div>
<?php } ?>
		</div>
		 <div class="media-body">
				<h3 class="media-title"><a href="<?php echo $link; ?>"><?php echo $this->item->title; ?></a></h3>
<?php
	if($this->item->introtext == ''){
		$newtext = strip_tags($this->item->fulltext, '<strong><a><em>');
	} else {
		if ($note == 'opencalls' || $note == 'bestpractices') {
			$newtext = strip_tags($this->item->introtext, '<br>');
		} else {
			$newtext = strip_tags($this->item->introtext, '<strong><a><em>');
		}
	}
?>
<?php
	if ($note == 'opencalls') {
		$attribs1  = json_decode($this->item->attribs);
		echo '<div class="thumbnail"><time>Deadline: '. @JHTML::_('date', $attribs1->opencall_date, 'd/m/Y') .'</time></div>';
	}
?>
				<p <?php echo ($note == 'opencalls' || $note == 'bestpractices' ? 'style="max-height:208px; overflow:hidden;"' : ''); ?>>
					<?php echo str_replace('<a ','<a target="_blank" ',$newtext); ?>
				</p>
				<a href="<?php echo $link; ?>"><?php echo JText::_('COM_CONTENT_FEED_READMORE'); ?></a>
		 </div>
	</div>
<?php
	if ($this->item->counter % 2 == 0) {
		echo '<div class="articles_by_two">&nbsp;</div>';
	}
	$this->item->counter++;
}
?>
<?php //echo $this->item->event->afterDisplayContent; ?>
