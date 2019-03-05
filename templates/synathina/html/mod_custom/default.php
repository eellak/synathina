<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$findme   = '<a ';
$findme1   = '<img ';
$pos = strpos($module->content, $findme);
$pos1 = strpos($module->content, $findme1);
if ($pos !== false || $pos1 !== false) {
	echo strip_tags($module->content,'<a><img>');
}else{
	echo $module->content;
}

?>
