<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_teams'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

if (!JFactory::getUser()->authorise('core.manage', 'com_teams')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('Teams');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
