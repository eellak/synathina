<?php
/**
 * @plugin		images
 * @script		images.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Plug-in to enable loading images component into content (e.g. articles)
 */
class plgSystemImages extends JPlugin
{
	public function onAfterInitialise()
	{
		$option 		= JRequest::getString( 'option', null );
		$task 			= JRequest::getString( 'task', null );
		
		$session_name 	= JRequest::getString( 'session_name', null );
		$session_id 	= JRequest::getString( 'session_id', null );
		
		$user 			= JFactory::getUser();
		$user_id 		= (int) $user->get( 'id' );
		
		if( $option == 'com_di' && $task == 'ajax.upload' && !$user_id && $session_name && $session_id )
		{
			$session = &JFactory::getSession();
			
			$_COOKIE[ $session_name ] = $session_id;  //  3.0
			setcookie( $session_name, $session_id, time() + 3600 );  //  2.5
			
			$session->restart();
		}
	}
}
