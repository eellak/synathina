<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_timeline
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();

$document->addStyleSheet('http://steficon-demo.eu/messinia_demo/modules/mod_hotelsideslider/tmpl/css/jcarousel.basic.css');
$document->addScript('http://steficon-demo.eu/messinia_demo/modules/mod_hotelsideslider/tmpl/js/jquery.jcarousel.min.js');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list            = ModOverlayVideoHelper::getList($module, $params, $attribs);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_overlayvideo', $params->get('layout', 'default'));