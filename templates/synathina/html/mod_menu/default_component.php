<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';
$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

if ($item->menu_image)
{
	$item->params->get('menu_text', 1) ?
	$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
	$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
}
else
{
	$linktype = $item->title;
}
$extra_actions_url='';
if($item->component=='com_actions'){
	$user = JFactory::getUser();
	$isroot = $user->authorise('core.admin');

	$fields = ['aa.id', 'aa.action_date_start'];
	$query_where = "a.id>0  AND aa.action_id>0 ".($isroot==1?'AND a.published>=0':'AND a.published=1')." ";
	$order_by = "aa.action_date_start ASC";

	$actions = $activityClass->getActivitiesSubactivities($fields, $query_where, $order_by);

	$total = count($actions);
	$end=1;
	$counter=0;
	foreach($actions as $action){
		if($action->action_date_start>=date('Y-m-d').' 00:00:00'){
			$counter=$end;
			break;
		}
		$end++;
	}
	$extra_actions_url='?start='.(ceil($counter/6)*6 - 6);
}

switch ($item->browserNav)
{
	default:
	case 0:
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?><?php echo $extra_actions_url;?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
	// Use JavaScript "window.open"
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a>
<?php
		break;
}
