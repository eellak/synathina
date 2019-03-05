<?php
/**
 * @plugin		images
 * @script		images.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file' );

if( !class_exists( 'CallbackBody' ) ) require_once dirname( __FILE__ ) . DS . 'phpQuery-onefile.php';

global $_image_list;
global $_images_sizes;

/**
 * Plug-in to enable loading images component into content (e.g. articles)
 */
class plgContentImages extends JPlugin
{
	/*
	 * return featured image
	 */
	public static function getFeaturedImage( $id )
	{
		$images = plgContentImages::getImages( $id, true );

		if( is_array( $images ) )
		{
			foreach( $images as $item )
			{
				if( $item->featured )
				{
					return $item;
				}
			}
		}

		return null;
	}

	/*
	 * returns images
	 */
	public static function getImages( $id, $featured = false, $get_image_size = false )
	{
		global $_image_list;

		$sizes 	= plgContentImages::getImagesSizes();
		$list 	= null;

		$component_params 	= JComponentHelper::getParams( 'com_media' );
		$di_directory 		= 'di';
		$media_url 			= JUri::root() . $component_params->get( 'image_path' ) . '/' . $di_directory;
		$media_path 		= JPATH_ROOT . DS . $component_params->get( 'image_path' ) . DS . $di_directory;

		if( !isset( $_image_list[ $id ] ) )
		{
			$db = JFactory::getDBO();

			$query = "SELECT * FROM #__di_images WHERE object_id = '$id' ORDER BY ordering";
			$db->setQuery( $query );
			$_image_list[ $id ] = $db->loadObjectList();
		}


		if( is_array( $_image_list[ $id ] ) && is_array( $sizes ) )
		{
			foreach( $_image_list[ $id ] as &$item )
			{
				$item->title 		= (string) $item->title;
				$item->description 	= (string) $item->description;
				$item->link 		= (string) $item->link;
				$item->link_target 	= (string) $item->link_target;
				$item->src 			= new stdClass();

				foreach( $sizes as $size )
				{
					if( !empty( $size->indent ) )
					{
						$item->src->{$size->indent} = $media_url . '/' . $item->object_id . '_' . $item->object_image_id . '_' . $size->indent . '_' . $item->filename;

						if( $get_image_size && file_exists( $media_path . DS . $item->object_id . '_' . $item->object_image_id . '_' . $size->indent . '_' . $item->filename ) )
						{
							$item->info->{$size->indent} = getimagesize( $item->src->{$size->indent} );
						}
					}
				}

				if( !$featured && $item->featured )
				{
					//  do nothing...
				}
				else
				{
					$list[] = $item;
				}
			}
		}

		return $list;
	}

	/*
	 * return images sizes
	 */
	public static function getImagesSizes( $template_id = 'default' )
	{
		global $_images_sizes;

		if( !isset( $_images_sizes ) )
		{
			$db = JFactory::getDBO();

			//  retrieve image sizes
			$query = "
				SELECT
					*
				FROM
					#__di_images_sizes
				WHERE
					template_id = '$template_id'
			";
			$db->setQuery( $query );
			$_images_sizes = $db->loadObjectList();
		}

		return $_images_sizes;
	}

	/*
	 *  load images module for administrator, for 3.0 version
	 */
	public function onAfterRender()
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$option 	= JRequest::getCmd( 'option', '' );
        $view 		= JRequest::getCmd( 'view', '' );
		$body 		= JResponse::getBody();
		$theme 		= $this->params->get( 'theme', 'isis' );
		$user 		= JFactory::getUser();
		$version 	= new JVersion();

		//  check access
		if( !$user->authorise( 'core.manage', 'com_di' ) )
		{
			return;
		}

		if( $app->isAdmin() && $option == 'com_content' && $view == 'article' )
		{
			//  insert images
			$title 			= '';
			$title_slug 	= '';
			$body_images 	= '';

			$modules 		= JModuleHelper::getModules( 'images' );

			if( $modules )
			{
				$title 			= $modules[ 0 ]->title;
				$title_slug 	= str_replace( ' ', '_', strtolower( $title ) );
				$body_images 	= JModuleHelper::renderModule( $modules[ 0 ] );

				$pq_doc 		= phpQuery::newDocument( $body );

				phpQuery::selectDocument( $pq_doc );

				if( !empty( $body_images ) )
				{
					switch( $theme )
					{
						case 'isis' :
						default :
						{
							//  for 3.0 version add tab link
							if( strpos( substr( $version->getShortVersion(), 0, 3 ), '3.0' ) !== FALSE )
							{
								pq( '.nav-tabs:first' )->append( '<li><a href="#' . $title_slug . '" data-toggle="tab">' . $title . '</a></li>' );

								pq( '.tab-content:first' )->append( '<div class="tab-pane" id="' . $title_slug . '">' . $body_images . '</div>' );

								$body = trim( pq( 'html' )->htmlOuter() );
							}
							break;
						}
					}

					JResponse::setBody( $body );
				}
			}
		}

		return '';
	}

	/*
	 *  load images module for administrator, 3.1 and above
	 */
	public function onBeforeRender()
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$option 	= JRequest::getCmd( 'option', '' );
        $view 		= JRequest::getCmd( 'view', '' );
		$body 		= JResponse::getBody();
		$theme 		= $this->params->get( 'theme', 'isis' );
		$user 		= JFactory::getUser();
		$version 	= new JVersion();

		//  check access
		if( !$user->authorise( 'core.manage', 'com_di' ) )
		{
			return;
		}

		if( $app->isAdmin() && $option == 'com_content' && $view == 'article' )
		{
			//  insert images
			$title 			= '';
			$title_slug 	= '';

			$modules 		= JModuleHelper::getModules( 'images' );

			if( $modules )
			{
				$title 			= $modules[ 0 ]->title;
				$title_slug 	= str_replace( ' ', '_', strtolower( $title ) );
				$body_images 	= JModuleHelper::renderModule( $modules[ 0 ] );

				if( !empty( $title ) )
				{
					switch( $theme )
					{
						case 'isis' :
						default :
						{
							//  if version is 3.1 and/or above
							if( strcmp( $version->getShortVersion(), '3.1' ) >= 0 )
							{
								$myTabContent 	= JLayoutHelper::render( 'libraries.cms.html.bootstrap.starttabset', array( 'selector' => 'myTab' ) );
								$content 		= $doc->getBuffer( 'component' );
								$content 		= str_replace( $myTabContent, $myTabContent . JHtml::_( 'bootstrap.addTab', 'myTab', $title_slug, $title ) . $body_images . JHtml::_( 'bootstrap.endTab' ), $content );

								$doc->setBuffer( $content, array( 'type' => 'component', 'name' => '', 'title' => '' ) );

								break;
							}
						}
					}
				}
			}
		}

		return '';
	}

	/*
	 * load front end images module
	 */
	public function onContentAfterDisplay( $context, &$row, &$params, $limitstart = null )
	{
		$option = JRequest::getCmd( 'option', '' );
        $view 	= JRequest::getCmd( 'view', '' );

		if( $context == 'com_content.article' && ( $modules = JModuleHelper::getModules( 'images' ) ) )
		{
			return JModuleHelper::renderModule( $modules[ 0 ] );
		}

		return '';
	}

	/*
	 * update images properties after content is saved
	 */
	public function onContentAfterSave( $context, $article, $isNew )
	{
		if( $context == 'com_content.article' )
		{
			$app = &JFactory::getApplication();

			if( $app->input->get( 'task' ) == 'save2copy' )
			{
				$this->copy( $context, $article, $isNew );
			}
			else
			{
				$this->save( $context, $article, $isNew );
			}
		}
	}

	/*
	 * copy images
	 */
	public function copy( $context, $article, $isNew )
	{
		//  application
		$app 		= &JFactory::getApplication();
		$data 		= JRequest::getVar( 'jform', null );
		$date 		= date( 'Y-m-d H:i:s' );

		//  old content id
		$content_id = (int) $data[ 'id' ];

		//  new content id
		$new_id = (int) $article->id;

		if( $content_id && $new_id )
		{
			//  db object
			$db = &JFactory::getDbo();

			$component_params 	= &JComponentHelper::getParams( 'com_media' );
			$di_directory 		= 'di';
			$media_path 		= JPATH_ROOT . DS . $component_params->get( 'image_path' ) . DS . $di_directory;

			$images = self::getImages( $content_id, true );

			if( $images )
			{
				//  get sizes
				$sizes = self::getImagesSizes();

				foreach( $images as $image )
				{
					//  insert record to db
					$query = "
						INSERT INTO #__di_images (
							object_id,
							state,
							filename,
							title,
							description,
							featured,
							date_created,
							ordering,
							link,
							link_target
						)
						VALUES (
							'$new_id',
							'" . $image->state . "',
							'" . $image->filename . "',
							'" . $image->title . "',
							'" . $image->description . "',
							'" . $image->featured . "',
							'$date',
							'" . $image->ordering . "',
							'" . $image->link . "',
							'" . $image->link_target . "'
						);
					";
					$db->setQuery( $query );
					$db->query();

					$object_image_id 	= $db->insertid();
					$source_name 		= $media_path . DS . $content_id . '_' . $image->object_image_id . '_' . $image->filename;

					if( $object_image_id && JFile::exists( $source_name ) )
					{
						$target_name = $media_path . DS . $new_id . '_' . $object_image_id . '_' . $image->filename;

						//  copy image
						JFile::copy( $source_name, $target_name );

						if( JFile::exists( $target_name ) )
						{
							//  copy image resizes
							foreach( $sizes as $size )
							{
								$size_source_name = $media_path . DS . $content_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;
								$size_target_name = $media_path . DS . $new_id . '_' . $object_image_id . '_' . $size->indent . '_' . $image->filename;

								JFile::copy( $size_source_name, $size_target_name );
							}
						}
					}
				}
			}
		}
	}

	/*
	 * save images
	 */
	public function save( $context, $article, $isNew )
	{
		$session 		= &JFactory::getSession();
		$db 			= &JFactory::getDBO();
		$data 			= JRequest::getVar( 'jform', null );
		$content_id 	= $article->id;

		//  media component parameters
		$com_media_params 	= &JComponentHelper::getParams( 'com_media' );
		$di_directory 		= 'di';
		$media_path 		= JPATH_ROOT . DS . $com_media_params->get( 'image_path' ) . DS . $di_directory;

		if( $content_id && isset( $data[ 'object_image_id' ] ) && is_array( $data[ 'object_image_id' ] ) )
		{
			foreach( $data[ 'object_image_id' ] as $key => $value )
			{
				$value = (int) $value;

				if( empty( $value ) )
				{
					unset( $data[ 'object_image_id' ][ $key ] );
				}
			}

			if( count( $data[ 'object_image_id' ] ) )
			{
				//  get current uploaded image list
				$query = "
					SELECT
						*
					FROM
						#__di_images
					WHERE
						object_image_id IN ( '" . implode( "', '", $data[ 'object_image_id' ] ) . "' )
				";
				$db->setQuery( $query );
				$images = $db->loadObjectList();

				//  update object_id for images with random object_id
				$query = "
					UPDATE
						#__di_images
					SET
						`object_id` = '$content_id'
					WHERE
						object_image_id IN ( '" . implode( "', '", $data[ 'object_image_id' ] ) . "' )
				";
				$db->setQuery( $query );
				$db->query();

				if( $db->getAffectedRows() )
				{
					$session->set( 'DI_OBJECT_ID', 0 );

					//  retrieve image sizes
					$query = "
						SELECT
							*
						FROM
							#__di_images_sizes
						WHERE
							template_id = 'default'
					";
					$db->setQuery( $query );
					$sizes = $db->loadObjectList();

					if( is_array( $images ) )
					{
						foreach( $images as $image )
						{
							$source_file = $media_path . DS . $image->object_id . '_' . $image->object_image_id . '_' . $image->filename;
							$target_file = $media_path . DS . $content_id . '_' . $image->object_image_id . '_' . $image->filename;

							rename( $source_file, $target_file );

							if( is_array( $sizes ) )
							{
								foreach( $sizes as $size )
								{
									$source_size_file = $media_path . DS . $image->object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;
									$target_size_file = $media_path . DS . $content_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;

									rename( $source_size_file, $target_size_file );

									//  updatee featured image
									if( $image->featured && $size->indent == 'thumb' )
									{
										$article->images->image_intro = $media_url . '/' . $content_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;

										$query = "UPDATE #__content SET images = replace(images, '" . $image->object_id . "', '$content_id')";
										$db->setQuery( $query );
										$db->query();
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/*
	 * delete images when content is deleted
	 */
	public function onContentBeforeDelete( $context,  $data )
	{
		$db 						= &JFactory::getDBO();
		$media_component_params 	= &JComponentHelper::getParams( 'com_media' );
		$component_params 			= &JComponentHelper::getParams( 'com_di' );

		$di_directory 				= 'di';
		$media_path 				= JPATH_ROOT . DS . $media_component_params->get( 'image_path' ) . DS . $di_directory;

		if( $context == 'com_content.article' && $component_params->get( 'delete_images_on_content_delete' ) && $data->id )
		{
			$query = "
				SELECT
					*
				FROM
					#__di_images
				WHERE
					object_id = '" . $data->id . "'
			";
			$db->setQuery( $query );
			$images = $db->loadObjectList();

			if( $images )
			{
				//  retrieve image sizes
				$query = "
					SELECT
						*
					FROM
						#__di_images_sizes
					WHERE
						template_id = 'default'
				";
				$db->setQuery( $query );
				$sizes = $db->loadObjectList();

				foreach( $images as $image )
				{
					$filename = $media_path . DS . $image->object_id . '_' . $image->object_image_id . '_' . $image->filename;

					JFile::delete( $filename );

					if( is_array( $sizes ) )
					{
						foreach( $sizes as $size )
						{
							$filename = $media_path . DS . $image->object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;

							JFile::delete( $filename );
						}
					}
				}
			}
		}
	}
}
