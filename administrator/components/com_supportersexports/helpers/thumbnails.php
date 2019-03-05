<?php
/**
 * @package     admin
 * @subpackage	component
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// error reporting
error_reporting(E_ALL);

class BooksMapsHelperThumbs
{
	public static function createThumbnails($images, $type)
	{
                $images = json_decode($images);

                foreach(get_object_vars($images) as $key => $value) {

                        if (preg_match('/front_image|back_image|image_/', $key) && $value != '' ) {
                                BooksMapsHelperThumbs::createThumbs($value, $type);
                        }

                };

	}

    public static function createThumbs($image, $type){ 

                $pathToThumbs = JPATH_BASE . '/../images/' . $type . '_auto_thumbs/';
                $pathToImages =  JPATH_BASE . '/../' . pathinfo($image, 1) . '/';
                $imageName =  pathinfo($image, 2);

                BooksMapsHelperThumbs::createThumb($pathToImages, $pathToThumbs, $imageName, pathinfo($image, 8) . '.thumb1.' . pathinfo($image, 4), 'height', 220, 160);
                BooksMapsHelperThumbs::createThumb($pathToImages, $pathToThumbs, $imageName, pathinfo($image, 8) . '.thumb2.' . pathinfo($image, 4), 'height', 100, 60);
                BooksMapsHelperThumbs::createThumb($pathToImages, $pathToThumbs, $imageName, pathinfo($image, 8) . '.thumb3.' . pathinfo($image, 4), 'width', 692, 600);

        }

   public static function createThumb($pathToImages, $pathToThumbs, $fname, $tname, $calculate, $thumbWidth, $thumbHeight) {

                // parse path for the extension
                $info = pathinfo($pathToImages . $fname);
                // continue only if this is a JPEG image
                if (strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg' || strtolower($info['extension']) == 'pjpeg') {

						 // attempt to load image

                        $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );

                        // check if it failed to load the image
                        if(!$img) {

                       
                                // create a blank image
                                $img  = imagecreatetruecolor(150, 30);
                                $bgc = imagecolorallocate($img, 255, 255, 255);
                                $tc  = imagecolorallocate($img, 0, 0, 0);

                                imagefilledrectangle($img, 0, 0, 150, 30, $bgc);

                                // output an error message
                                imagestring($img, 1, 5, 5, 'Error loading ' . $fname, $tc);

                                // save image into a file
                                imagejpeg( $img, "{$pathToThumbs}{$fname}" );

                        } else {

                       
                                // get image size
                                $width = imagesx( $img );
                                $height = imagesy( $img );

                                // calculate thumbnail size by width
                                if ($calculate == 'width') {

                                        $new_width = $thumbWidth;
                                        $new_height = floor( $height * ( $thumbWidth / $width ) );

                                }

                                // calculate thumbnail size by height
                                if ($calculate == 'height') {

                                        $new_height = $thumbHeight;
                                        $new_width = floor( $width * ( $thumbHeight / $height ) );
                                }

                                // create a new temporary image
                                $tmp_img = imagecreatetruecolor( $new_width, $new_height );

                                // copy and resize old image into new image
                                imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

                                // save thumbnail into a file
                                imagejpeg( $tmp_img, "{$pathToThumbs}{$tname}" );

                                // Free up memory
                                imagedestroy( $tmp_img );

                        }

                }

                // continue only if this is a PNG image
                if (strtolower($info['extension']) == 'png'){

                        // attempt to load image
                        $img = @imagecreatefrompng( "{$pathToImages}{$fname}" );

                        // check if it failed to load the image
                        if(!$img) {

                                // create a blank image
                                $img  = imagecreatetruecolor(150, 30);
                                $bgc = imagecolorallocate($img, 255, 255, 255);
                                $tc  = imagecolorallocate($img, 0, 0, 0);

                                imagefilledrectangle($img, 0, 0, 150, 30, $bgc);

                                // output an error message
                                imagestring($img, 1, 5, 5, 'Error loading ' . $fname, $tc);

                                // save image into a file
                                imagepng( $img, "{$pathToThumbs}{$fname}" );

                        } else {

                                // get image size
                                $width = imagesx( $img );
                                $height = imagesy( $img );

                                // calculate thumbnail size by width
                                if ($calculate == 'width') {

                                        $new_width = $thumbWidth;
                                        $new_height = floor( $height * ( $thumbWidth / $width ) );

                                }

                                // calculate thumbnail size by height
                                if ($calculate == 'height') {

                                        $new_height = $thumbHeight;
                                        $new_width = floor( $width * ( $thumbHeight / $height ) );
                                }

                                // create a new temporary image
                                $tmp_img = imagecreatetruecolor( $new_width, $new_height );

                                // copy and resize old image into new image
                                imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

                                // save thumbnail into a file
                                imagepng( $tmp_img, "{$pathToThumbs}{$fname}" );

                                // Free up memory
                                imagedestroy( $tmp_img );

                        }

                }

                // continue only if this is a GIF image
                if (strtolower($info['extension']) == 'gif'){

                        // attempt to load image
                        $img = @imagecreatefromgif( "{$pathToImages}{$fname}" );

                        // check if it failed to load the image
                        if(!$img) {

                                // create a blank image
                                $img  = imagecreatetruecolor(150, 30);
                                $bgc = imagecolorallocate($img, 255, 255, 255);
                                $tc  = imagecolorallocate($img, 0, 0, 0);

                                imagefilledrectangle($img, 0, 0, 150, 30, $bgc);

                                // output an error message
                                imagestring($img, 1, 5, 5, 'Error loading ' . $fname, $tc);

                                // save image into a file
                                imagegif( $img, "{$pathToThumbs}{$fname}" );

                        } else {

                                // get image size
                                $width = imagesx( $img );
                                $height = imagesy( $img );

                                // calculate thumbnail size by width
                                if ($calculate == 'width') {

                                        $new_width = $thumbWidth;
                                        $new_height = floor( $height * ( $thumbWidth / $width ) );

                                }

                                // calculate thumbnail size by height
                                if ($calculate == 'height') {

                                        $new_height = $thumbHeight;
                                        $new_width = floor( $width * ( $thumbHeight / $height ) );
                                }

                                // create a new temporary image
                                $tmp_img = imagecreatetruecolor( $new_width, $new_height );

                                // copy and resize old image into new image
                                imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

                                // save thumbnail into a file
                                imagegif( $tmp_img, "{$pathToThumbs}{$fname}" );

                                // Free up memory
                                imagedestroy( $tmp_img );
                        }

                }

        }
}