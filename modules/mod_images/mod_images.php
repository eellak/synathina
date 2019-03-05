<?php
/**
 * @module		com_di
 * @script		mod_images.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

if( !defined( 'DS' ) )
{
	define( 'DS', DIRECTORY_SEPARATOR );
}

// Include dependancies.
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'helper.php';

//  variables
$id = JRequest::getVar( 'id', JRequest::getCmd( 'id', '' ) );

if( !empty( $id ) && strpos( $id, ':' ) !== FALSE )
{
	$id_parts = explode( ':', $id );
	
	if( !empty( $id_parts[ 0 ] ) )
	{
		$id = (int) $id_parts[ 0 ];
	}
}


//  media component parameters
$cparams 		= &JComponentHelper::getParams( 'com_media' );

//  full object image path
$di_directory 		= 'di';
$media_path 		= JPATH_ROOT . DS . $cparams->get( 'image_path' ) . DS . $di_directory;  //  without trailing slash
$media_url 			= JUri::root() . $cparams->get( 'image_path' ) . DS . $di_directory;  //  full images url without trailing slash

$list = modImagesHelper::getList( $id );

require JModuleHelper::getLayoutPath( 'mod_images', $params->get( 'layout', 'default' ) );