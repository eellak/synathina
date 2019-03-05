<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_actions
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_actions
 *
 * @since  3.3
 */

/**
 * Content router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function OpencallsBuildRoute(&$query)
{
       $segments = array();
       if (isset($query['view']))
       {
                $segments[] = $query['view'];
                unset($query['view']);
       }
       if (isset($query['id']))
       {
                $segments[] = $query['id'];
                unset($query['id']);
       };
       return $segments;
}

function OpencallsParseRoute($segments)
{
       $vars = array();
       switch($segments[0])
       {
               case 'edit':
                       $vars['view'] = 'edit';
                       $id = explode(':', @$segments[1]);
                       $vars['id'] = (int) $id[0];
                       break;												 
               case 'Opencalls':
                       $vars['view'] = 'opencalls';
                       $id = explode(':', @$segments[1]);
                       $vars['id'] = (int) $id[0];
                       break;												 
       }
       return $vars;
}
