<?php
/**
 * @module		com_di
 * @script		ajax.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

//  enable error reporting
error_reporting( E_ERROR );

//  include path
if( strtolower( substr( php_uname(), 0, 3 ) ) == 'win' )
{
	ini_set( 'include_path', '.;' . JPATH_COMPONENT . '/;' . JPATH_COMPONENT . '/libraries/pear' );
}
else
{
	ini_set( 'include_path', '.:' . JPATH_COMPONENT . '/:' . JPATH_COMPONENT . '/libraries/pear' );
}

//  images plugin
require_once JPATH_ROOT . '/plugins/content/images/images.php';
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

//  image trasform
require_once JPATH_COMPONENT . '/libraries/pear/Image/Transform.php';

/**
 * ajax actions controller class.
 */
class DiControllerAjax extends JControllerAdmin
{
	private $response;

	//  construct
	public function DiControllerAjax()
	{
		$this->response 			= new stdClass();
		$this->response->status 	= -1;
		$this->response->message 	= '';
		$this->response->data 		= '';

		//$task = JRequest::getCmd( 'task', null );
		$task = JFactory::getApplication()->input->get( 'task', null );
		if( $task && method_exists( 'DiControllerAjax', $task ) )
		{
			$this->$task();
		}

		//  set headers
		$this->setHeaders();

		//  output
		$this->output();
	}
	//  /construct

	//  upload images
	public function upload()
	{
		$db 		= &JFactory::getDBO();
		$object_id 	= JRequest::getInt( 'object_id', 0 );

		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.create', 'com_di' ) )
		{
			return;
		}

		if( $user_id )
		{
			//  retrieve image sizes
			$sizes = plgContentImages::getImagesSizes();

			//  media component parameters
			$media_component_params = &JComponentHelper::getParams( 'com_media' );
			$di_component_params 	= &JComponentHelper::getParams( 'com_di' );

			$di_directory 		= 'di';
			$media_path 		= JPATH_ROOT . DS . $media_component_params->get( 'image_path' ) . DS . $di_directory;
			$media_url 			= JUri::root() . DS . $media_component_params->get( 'image_path' ) . DS . $di_directory;
			$quality 			= $di_component_params->get( 'quality', 80 );

			//  get allowed image extensions
			$image_extensions 	= $media_component_params->get( 'image_extensions' );
			if( !empty( $image_extensions ) )
			{
				$image_extensions = explode( ',', $image_extensions );
			}

			$source_file = isset( $_FILES[ 'Filedata' ][ 'tmp_name' ] ) ? $_FILES[ 'Filedata' ][ 'tmp_name' ] : null;

			$filename 		= $this->slug( htmlspecialchars( $_FILES[ 'Filedata' ][ 'name' ], ENT_COMPAT, 'UTF-8' ) );
			$target_name 	= md5( time() );
			$target_file 	= $media_path . DS . $target_name;
			$path_info 		= pathinfo( $filename );

			if( !empty( $source_file ) && in_array( $path_info[ 'extension' ], $image_extensions ) )
			{
				//  check if directory exists
				if( !file_exists( $media_path ) )
				{
					JFolder::create( $media_path );
				}

				//  upload file
				JFile::upload( $source_file, $target_file );

				if( JFile::exists( $target_file ) )
				{
					chmod( $target_file, 0755 );

					$query = "
						INSERT INTO #__di_images (
							object_id,
							filename
						)
						VALUES (
							'" . $object_id . "',
							'" . $filename . "'
						);
					";
					$db->setQuery( $query );
					$db->query();

					$object_image_id = $db->insertid();

					if( $object_image_id )
					{
						$new_name = $object_id . '_' . $object_image_id . '_' . $filename;

						rename( $target_file, $media_path . DS . $new_name );

						$it =& Image_Transform::factory( 'GD' );
						if( PEAR::isError( $it ) )
						{
							var_dump( $it->getMessage() );
						}

						foreach( $sizes as $size ) {
							$this->createImage(
								$it,
								$media_path . DS . $new_name,
								$media_path . DS .  $object_id . '_' . $object_image_id . '_' . $size->indent . '_' . $filename,
								$size->width,
								$size->height,
								$quality,
								null,
								$size->crop
							);
						}

						$this->setResponse( 'status', 1 );
						$this->setResponse( 'message', 'SUCCESS' );
						$this->setResponse( 'data', $media_url . DS . $new_name );
					}
				}
			}
		}
	}
	//  /upload images

	//  change order
	public function order()
	{
		$nli 		= JRequest::getVar( 'nli', null );
		$object_id 	= JRequest::getInt( 'object_id', null );

		$db 		= &JFactory::getDbo();
		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}

		if( isset( $nli ) && is_array( $nli ) && $object_id && $user_id )
		{
			$query = "SELECT object_image_id FROM #__di_images WHERE object_id = '" . $object_id . "'";
			$db->setQuery( $query );
			$images = $db->loadObjectList();

			$images_ids = '';
			if( is_array( $images ) )
			{
				foreach( $images as $image )
				{
					if( $images_ids !== '')
					{
						$images_ids .=  ',' . $image->object_image_id;
					}
					else
					{
						$images_ids .=  $image->object_image_id;
					}
				}
			}

			$items = '';
			foreach( $nli as $item )
			{
				if( $items !== '' )
				{
					$items .= ',' . (int)$item;
				}
				else
				{
					$items .= (int)$item;
				}
			}

			$query = 'UPDATE #__di_images SET ordering = FIND_IN_SET(object_image_id, "' . $items . '") WHERE object_image_id IN (' . $images_ids . ')';
			$db->setQuery( $query );
			$db->query();

			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /change order

	//  remove image
	public function remove()
	{
		$object_image_id = JRequest::getString( 'object_image_id', '' );

		$db 		= &JFactory::getDbo();
		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		//  media component parameters
		$component_params 	= &JComponentHelper::getParams( 'com_media' );
		$di_directory 		= 'di';
		$media_path 		= JPATH_ROOT . DS . str_replace( '/', DS, $component_params->get( 'image_path' ) ) . DS . $di_directory;

		if( !$user->authorise( 'core.delete', 'com_di' ) )
		{
			return;
		}

		if( $object_image_id && $user_id )
		{
			$images 	= null;
			$affected 	= 0;
			$data 		= null;

			//  retrieve image sizes
			$sizes = plgContentImages::getImagesSizes();

			if( strpos( $object_image_id, ',' ) !== FALSE )
			{
				$object_image_id = explode( ',', $object_image_id );

				foreach( $object_image_id as $key => $value )
				{
					$object_image_id[ $key ] = (int) $value;

					if( empty( $object_image_id[ $key ] ) )
					{
						unset( $object_image_id[ $key ] );
					}
				}

				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id IN ( '" . implode( "', '", $object_image_id ) . "' )";
				$db->setQuery( $query );
				$images = $db->loadObjectList();
			}
			else
			{
				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id = '$object_image_id'";
				$db->setQuery( $query );
				$image = $db->loadObject();

				$images[] = $image;
			}

			if( $images )
			{
				foreach( $images as $item )
				{
					$target_file = $media_path . DS . $item->object_id . '_' . $item->object_image_id . '_' . $item->filename;

					JFile::delete( $target_file );

					if( !JFile::exists( $target_file ) )
					{
						if( is_array( $sizes ) )
						{
							foreach( $sizes as $size )
							{
								JFile::delete( $media_path . DS .  $item->object_id . '_' . $item->object_image_id . '_' . $size->indent . '_' . $item->filename );
							}
						}

						//  delete from database
						$query = "DELETE FROM #__di_images WHERE object_image_id = '" . $item->object_image_id . "'";
						$db->setQuery( $query );
						$db->query();

						$affected += $db->getAffectedRows();
						$data[] = $item->object_image_id;
					}
				}

				$this->setResponse( 'status', $affected );
				$this->setResponse( 'data', $data );
				$this->setResponse( 'message', 'SUCCESS' );
			}
		}
	}
	//  /remove image

	//  resize images
	public function resize()
	{
		$db 		= &JFactory::getDBO();
		$object_id 	= JRequest::getInt( 'object_id', 0 );

		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}

		if( $user_id )
		{
			$resized 	= 0;
			$data 		= null;
			$images 	= null;

			//  media component parameters
			$media_component_params = &JComponentHelper::getParams( 'com_media' );
			$di_component_params 	= &JComponentHelper::getParams( 'com_di' );

			$di_directory 		= 'di';
			$media_path 		= JPATH_ROOT . DS . $media_component_params->get( 'image_path' ) . DS . $di_directory;
			$media_url 			= JUri::root() . DS . $media_component_params->get( 'image_path' ) . DS . $di_directory;
			$quality 			= (int) $di_component_params->get( 'quality', 80 );

			$it =& Image_Transform::factory( 'GD' );
			if( PEAR::isError( $it ) )
			{
				var_dump( $it->getMessage() );
			}

			$object_image_id = JRequest::getVar( 'object_image_id', null );

			if( is_array( $object_image_id ) )
			{
				foreach( $object_image_id as $key => $value )
				{
					$object_image_id[ $key ] = (int) $value;

					if( empty( $object_image_id[ $key ] ) )
					{
						unset( $object_image_id[ $key ] );
					}
				}
			}

			if( is_array( $object_image_id ) )
			{
				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id IN ( '" . implode( "', '", $object_image_id ) . "' )";
				$db->setQuery( $query );
				$images = $db->loadObjectList();
			}

			if( $images )
			{
				//  retrieve image sizes
				$sizes = plgContentImages::getImagesSizes();

				if( isset( $object_id ) && is_array( $images ) && is_array( $sizes ) )
				{
					foreach( $images AS $image )
					{
						foreach( $sizes AS $size )
						{
							$filename = $media_path . DS .  $object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;

							//  delete old
							if( file_exists( $filename ) )
							{
								unlink( $filename );
							}

							//  create resized image
							$this->createImage(
								$it,  //  kai bus sukurtas komponentas pasalinti
								$media_path . DS . $object_id . '_' . $image->object_image_id . '_' . $image->filename,
								$filename,
								$size->width,
								$size->height,
								$quality,
								null,
								$size->crop
							);

							if( file_exists( $filename ) )
							{

								$resized++;
								$data[] = $media_url . DS . $object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;
							}
						}
					}
				}

				$this->setResponse( 'status', $resized );
				$this->setResponse( 'data', $data );
				$this->setResponse( 'message', 'SUCCESS' );
			}
		}
	}
	//  /resize images

	//  change featured state
	public function featured()
	{
		$db 		= &JFactory::getDBO();
		$object_image_id 	= JRequest::getInt( 'object_image_id', 0 );
		$value 		= JRequest::getInt( 'value', 0 );

		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}

		if( $user_id && $object_image_id )
		{
			$query = "
				SELECT
					object_id
				FROM
					#__di_images
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$object_id = $db->loadResult();

			//  unset featured for same object_id images
			$query = "
				UPDATE
					#__di_images
				SET
					`featured` = '0'
				WHERE
					object_id = '$object_id'
			";
			$db->setQuery( $query );
			$db->query();

			//  set featured
			$query = "
				UPDATE
					#__di_images
				SET
					`featured` = '$value'
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$db->query();

			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /change featured state

	//  update image
	public function update()
	{
		$db 				= &JFactory::getDBO();
		$object_image_id 	= JRequest::getInt( 'object_image_id', 0 );

		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}

		if( $user_id && $object_image_id )
		{
			$title = JRequest::getString( 'title', '' );
			$description = JRequest::getString( 'description', '' );
			$link = JRequest::getString( 'link', '' );
			$link_target = JRequest::getString( 'link_target', '' );

			$title = $db->escape( $title );
			$description = $db->escape( $description );
			$link = $db->escape( $link );
			$link_target = $db->escape( $link_target );

			$featured = JRequest::getInt( 'featured', 0 );
			$state = JRequest::getInt( 'state', 0 );

			$query = "
				UPDATE
					#__di_images
				SET
					`title` = '$title',
					`description` = '$description',
					`link` = '$link',
					`link_target` = '$link_target',
					`featured` = '$featured',
					`state` = '$state'
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$db->query();

			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /update image

	//  get images
	public function getImages()
	{
		$object_id 	= JRequest::getInt( 'object_id', 0 );

		$user 		= &JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );

		if( !$user->authorise( 'core.manage', 'com_di' ) )
		{
			return;
		}

		if( $user_id && $object_id )
		{
			$list = plgContentImages::getImages( $object_id, true );
			$list_count = count( $list );

			if( $list_count )
			{
				$this->setResponse( 'status', $list_count );
				$this->setResponse( 'message', 'SUCCESS' );
				$this->setResponse( 'data', $list );
			}
		}
	}
	//  /get images

	//  sets
	//  set http headers
	private function setHeaders()
	{
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Content-type: text/html; charset=utf-8' );
		header( 'Content-type: application/json' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	}
	//  /set http headers

	//  set status
	private function setResponse( $key, $value )
	{
		$this->response->$key = $value;
	}
	//  /set status

	//  gets
	//  print output
	private function output()
	{
		echo json_encode( $this->response );
		exit();
	}

	//  make slug
	function slug( $string )
	{
		$string = preg_replace( '/[^\w\.-]/', '-', strtolower( $string ) );
		$string = preg_replace( '/-+/', "-", $string );

		return $string;
	}
	//  /make slug

	/*
	 *  create resized image
	 *  $it - Image_Transform::factory
	 *  $source - old image filename
	 *  $target - new image filename
	 *  $width - image width
	 *  $height - image height
	 *  $quality - image quality
	 *  $format - image output format
	 *  $crop - T/F to crop and center image or not
	 */
	function createImage( $it, $source, $target, $width, $height, $quality = '80', $format = null, $crop = 0, $x = null, $y = null )
	{
		$parts = explode( '.', strtolower( $source ) );
		$parts_count = count( $parts );

		$format = ( $parts_count > 1 ) ? strtoupper( $parts[ $parts_count - 1 ] ) : 'PNG';

		if( $format == 'JPG' )
		{
			$format = 'JPEG';
		}

		$it->load( $source );

		if( $crop )
		{
			if( $x !== null && $y !== null )
			{
				$it->crop( $width, $height, $x, $y );
			}
			else
			{
				$new_height = $it->getNewImageHeight();
				$new_width  = $it->getNewImageWidth();

				if( ( $width / $height ) > ( $new_width / $new_height ) )
				{
					$it->fitX( $width );
					$new_height = $it->getNewImageHeight();
					if ( $new_height > $height )
					{
						$tmp_height = round( ( $new_height - $height ) * 0.5 );
						$it->crop( $width, $height, 0, $tmp_height );
					}
				}
				else
				{
					$it->fitY( $height );
					$new_width = $it->getNewImageWidth();
					if ( $new_width > $width )
					{
						$tmp_width = round( ( $new_width - $width ) * 0.5 );
						$it->crop( $width, $height, $tmp_width, 0 );
					}
				}
			}
		}
		else
		{
			$it->fit( $width, $height );
		}

		$it->fit( $width, $height );
		$it->save( $target, $format, $quality );
		$it->free();

		// chmod
		chmod( $target, 0755 );
	}
	//  /create resized image
}