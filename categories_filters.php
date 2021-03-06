<?php
header('Content-type: application/json; charset=UTF-8');
/**
 * @package    Core.Site
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
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

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

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

// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->setStart($startTime, $startMem)->mark('afterLoad') : null;

//get lang variables
$lang=@$_REQUEST['lang'];
if($lang=='en'){
}else{
	$lang='el';
}


$db = JFactory::getDbo();
$i=0;
echo '[';

	$query = "SELECT * FROM #__team_activities WHERE published='1' ORDER BY name ASC ";
	$db->setQuery($query);
	$actions = $db->loadObjectList();
	foreach($actions as $action){
		$i++;
		echo '{
      "category" : {
        "name" : "'.($lang=='en'?$action->name_en:$action->name).'",
        "id" : "'.$action->id.'"
      }
    }'.($i==count($actions)?'':',');
	}


echo ']';
	
?>