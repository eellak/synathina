<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

$db	= JFactory::getDBO();		
?>
<nav class="breadcrumb breadcrumb--with-border">	
<ul class="inline-list">
	<?php
	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i++)
	{
		if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link == $list[$i - 1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Make a link if not the last item in the breadcrumbs
	$show_last = $params->get('showLast', 1);

	// Generate the trail
	foreach ($list as $key => $item) :	
		//get menu item notes
		$note='';
		$query = "SELECT note FROM #__menu WHERE id='".@$item->id."' ";
		$db->setQuery($query);
		$note = $db->loadResult();
		if ($key != $last_item_key && trim($item->name)!='Άρθρα') :
			// Render all but last item - along with separator ?>
			<li>
				<?php 
					if (!empty($item->link)) : 
						if($note=='practices'){
							$item->link='javascript:window.history.back()';
						}
				?>
					<a href="<?php echo $item->link; ?>"><?php echo $item->name; ?></a>
				<?php else : ?>
				<?php echo $item->name; ?>
				<?php endif; ?>

			</li>
		<?php elseif ($show_last) :
			// Render last item if reqd. ?>
			<li class="active">
				<?php echo $item->name; ?>
			</li>
		<?php endif;
	endforeach; ?>
</ul>
</nav>