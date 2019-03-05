<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Create some shortcuts.
$params    = &$this->item->params;
$n         = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Check for at least one editable article
$isEditable = false;

if (!empty($this->items))
{
	foreach ($this->items as $article)
	{
		if ($article->params->get('access-edit'))
		{
			$isEditable = true;
			break;
		}
	}
}

//connect to db
$db = JFactory::getDBO();
?>

<div class="l-press">
	<div class="module module--synathina">
		<div class="module-skewed">
			<div class="l-press">
				<div class="module-title">
					<h3><?php echo $this->category->params->get('page_title'); ?></h3>
				</div>
				<div class="list-group">
<?php
	foreach ($this->items as $i => $article) : 
		//get attachment
		$query = "SELECT url FROM #__attachments WHERE parent_entity='article' AND parent_id='".$article->id."' AND state=1 AND access=1 LIMIT 1";
		$db->setQuery($query);
		$attachment_url = $db->loadResult();	
		if($attachment_url!=''){
			$href=$attachment_url;
			$target='target="_blank"';
		}else{
			$href=JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language));
			$target='';
		}
		echo '<div class="list-group-item">
						 <div class="list-group-wrapper">
								<time class="list-item-time">'.JHTML::_('date', $article->created, 'd.m.Y').'</time>
								<a class="list-item-title" href="'.$href.'" '.$target.'>'.$article->title.($target==''?'':'<i class="fa fa-file-pdf-o"></i>').'</a>
						 </div>
					</div>';
	endforeach; 
?>
				</div>
			</div>
		</div>	
	</div>
</div>	