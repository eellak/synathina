<?php
/**
 * Exception class for errors.
 *
 * @package Thumbnailer
 * @author Michal Zukowski <michal@freelogic.pl>
 * @copyright Michal Zukowski
 * @access public
 */
class ThumbnailerException extends Exception {}

/**
 * <a href="http://www.phpcontext.com/thumbnailer/">Thumbnailer premium class.</a>
 *
 * Copyright (C) 2009/2011 by Michal Zukowski
 * Permission is hereby granted, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software
 * without restriction excluding the rights to copy, modify, merge, publish or distribute
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package Thumbnailer
 * @version 1.3.5
 * @author Michal Zukowski <michal@freelogic.pl>
 * @copyright Michal Zukowski
 */
class Thumbnailer
{
	/**
	 * Constant used in {@link round()}, {@link logoText()} and {@link logoPhoto()} methods.
	 */
	const TOP_LEFT=1;

	/**
	 * Constant used in {@link round()}, {@link logoText()} and {@link logoPhoto()} methods.
	 */
	const TOP_RIGHT=2;

	/**
	 * Constant used in {@link round()}, {@link logoText()} and {@link logoPhoto()} methods.
	 */
	const BOTTOM_LEFT=4;

	/**
	 * Constant used in {@link round()}, {@link logoText()} and {@link logoPhoto()} methods.
	 */
	const BOTTOM_RIGHT=8;

	/**
	 * Constant used in {@link round()} method.
	 */
	const ALL=15;

	/**
	 * Constant used in {@link logoText()} and {@link logoPhoto()} methods.
	 */
	const CENTER=0;

	/**
	 * List of allowed mime types.
	 * By default only allowed mime types are JPEG, GIF and PNG.
	 *
	 * @var array
	 * @since 1.2.5
	 */
	public static $allowed=array('image/jpeg','image/jpg','image/gif','image/png');

	/**
	 * Full path to the source image.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $file;

	/**
	 * Image file name.
	 *
	 * @var string
	 * @since 1.3.1
	 */
	public $filename;

	/**
	 * Image resource. Opened image source in {@link prepare()} will be held here.
	 *
	 * @var resource
	 * @since 1.0.0
	 */
	public $resource=null;

	/**
	 * Function used to open the image.
	 *
	 * @var callback
	 * @since 1.0.0
	 */
	public $openfunc;

	/**
	 * Thumb resource.
	 *
	 * @var resource
	 * @since 1.0.0
	 */
	public $thumb;

	/**
	 * Thumb width
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $thumb_width;

	/**
	 * Thumb height
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $thumb_height;

	/**
	 * Image mime type.
	 *
	 * @var string
	 * @since 1.3.1
	 */
	public $mime;

	/**
	 * Align setting for text/image watermarks.
	 *
	 * @var int
	 * @since 1.3.3
	 */
	public $logoAlign=self::BOTTOM_LEFT;

	/**
	 * Thumbnailer creator.
	 * Creates thumbnailer object for inline scripting, i.e.:
	 * <pre>
	 * Thumbnailer::create('photo.jpg')->thumFixed(120,90)->header()->save();
	 * </pre>
	 *
	 * @param string $file File path or url
	 * @return Thumbnailer
	 * @access public
	 * @since 1.3.3
	 */
	public static function &create($file)
	{
		return new Thumbnailer($file);
	}

	/**
	 * Creates Thumbnailer object using {@link prepare()}
	 *
	 * @param string $file Image path
	 * @since 1.0.0
	 * @access private
	 */
	public function __construct($file)
	{
		$this->prepare($file);
	}

	/**
	 * Memory release.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	public function __destruct()
	{
		if (is_resource($this->resource)) {
			@imagedestroy($this->resource);
		}
		if (is_resource($this->thumb)) {
			@imagedestroy($this->thumb);
		}
	}

	/**
	 * Prepares the image to be opened.
	 *
	 * Method checks if file actually exists. If not it checks if file is remotely based.
	 * If it is, Thumbnailer creates a temporary file and tries to download the file.
	 * When image is ready it tries to get its mime type to set save function and open function to be able to work and save the results.
	 * In the end Thumbnailer opens the image and is ready to operate.
	 *
	 * @param string $file Path
	 * @since 1.3.0
	 * @throws ThumbnailerException
	 * @access private
	 */
	public function prepare($file)
	{
		$this->file=realpath($file);
		$this->filename=basename($file);

		if ($this->file === false)
		{
			$scheme=parse_url($file);
			if ($scheme['scheme'] == 'http' || $scheme['scheme'] == 'ftp')
			{
				$temp_name=tempnam(sys_get_temp_dir(), 'tmp');
				$content=@file_get_contents($file);
				if ($content !== false)
				{
					if (@file_put_contents($temp_name, $content))
					{
						$this->file=$temp_name;
						$this->filename=basename($scheme['path']);
						unset($content);
					}
					else
					{
						throw new ThumbnailerException('Could not write image stream', 1);
					}
				}
				else
				{
					throw new ThumbnailerException('Could not get image stream', 2);
				}
			}
			else
			{
				throw new ThumbnailerException('Image does not exists', 3);
			}
		}

		$info=@getimagesize($this->file);
		if (empty($info) || !in_array($info['mime'], self::$allowed))
		{
			throw new ThumbnailerException('Wrong mime type or invalid image', 4);
		}

		// saving mime
		$this->mime=$info[2];
		$this->thumb_width=$info[0];
		$this->thumb_height=$info[1];

		// resolve open function
		switch($info[2])
		{
			case IMAGETYPE_JPEG	: {
				$this->openfunc='imagecreatefromjpeg';
				break;
			}
			case IMAGETYPE_GIF	: {
				$this->openfunc='imagecreatefromgif';
				break;
			}
			case IMAGETYPE_PNG	: {
				$this->openfunc='imagecreatefrompng';
				break;
			}
			default:throw new ThumbnailerException('Cannot find save/open function for mime', 5);
		}

		$f =& $this->openfunc;
		$this->resource=$f($this->file);
	}

	/**
	 * Creates scaled thumbnail of maximum width $width and maximum height $height.
	 *
	 * @param int $width Maximum width
	 * @param int $height Maximum height
	 * @return Thumbnailer
	 * @since 1.0.0
	 * @access public
	 * @throws ThumbnailerException
	 */
	public function &thumbSymmetric($width, $height)
	{
		$old_width=imagesx($this->resource);
		$old_height=imagesy($this->resource);

		// if requested size is too large then do nothing
		if ($width > $old_width && $height > $old_height) {
			return $this;
		}

		$ratio=max($old_width, $old_height)/min($old_width, $old_height);
		if ($old_height > $old_width)
		{
			$new_height=$height;
			$new_width=$height/$ratio;
		}
		elseif ($old_height < $old_width)
		{
			$new_width=$width;
			$new_height=$width/$ratio;
		}
		else
		{
			$new_width=$width;
			$new_height=$height;
		}

		$new_width=round($new_width);
		$new_height=round($new_height);

		$this->thumb=$this->createImage($new_width, $new_height);
		$this->im($this->thumb, $this->resource, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
		$this->thumb_width=$new_width;
		$this->thumb_height=$new_height;

		return $this;
	}

	/**
	 * Create a thumbnail with fixed width and scaled height.
	 *
	 * @param int $width
	 * @return Thumbnailer
	 * @access public
	 * @since 1.2.6
	 */
	public function &thumbSymmetricWidth($width)
	{
		$old_width=imagesx($this->resource);
		$old_height=imagesy($this->resource);

		// just return the image when no change is possible
		if ($width > $old_width) {
			return $this;
		}

		$percent=$old_width/$width;
		$newHeight=floor($old_height/$percent);

		$this->thumbFixed($width, $newHeight);
		return $this;
	}

	/**
	 * Create a thumbnail with fixed height and scaled width.
	 *
	 * @param int $height
	 * @return Thumbnailer
	 * @access public
	 * @since 1.2.6
	 */
	public function &thumbSymmetricHeight($height)
	{
		$old_width=imagesx($this->resource);
		$old_height=imagesy($this->resource);

		// just return the image when no change is possible
		if ($height > $old_height) {
			return $this;
		}

		$newWidth=floor($old_width/($old_height/$height));
		$this->thumbFixed($newWidth, $height);

		return $this;
	}

	/**
	 * Create a square thumbnail.
	 *
	 * @param int $size Width and height size
	 * @return Thumbnailer
	 * @since 1.0.0
	 * @access public
	 */
	public function &thumbSquare($size)
	{
		$old_width=imagesx($this->resource);
		$old_height=imagesy($this->resource);

		if ($size >= min($old_width, $old_height)) {
			return $this;
		}

		// if photo is wide
		if ($old_height > $old_width)
		{
			$this->thumbSymmetricWidth($size);
			$y1=floor(($this->thumb_height-$size)/2);

			$tmp=$this->createImage($size, $size);
			$this->im($tmp, $this->thumb, 0, 0, 0, $y1, $size, $size, $size, $size);
			$this->thumb=$tmp;
		}
		// if photo is tall
		elseif ($old_height < $old_width)
		{
			$this->thumbSymmetricHeight($size);
			$x2=floor(($this->thumb_width-$size)/2);
			$tmp=$this->createImage($size, $size);
			$this->im($tmp, $this->thumb, 0, 0, $x2, 0, $size, $size, $size, $size);
			$this->thumb=$tmp;
		}
		// if photo is square
		else
		{
			$tmp=$this->createImage($size, $size);
			$this->im($tmp, $this->resource, 0, 0, 0, 0, $size, $size, $old_width, $old_height);
			$this->thumb=$tmp;
		}

		$this->thumb_height=$size;
		$this->thumb_width=$size;
		return $this;
	}

	/**
	 * Create a fixed size thumbnail.
	 *
	 * @param int $width Thumb width
	 * @param int $height Thumb height
	 * @return Thumbnailer
	 * @since 1.2.0
	 * @access public
	 */
	public function &thumbFixed($width, $height)
	{
		$img_width=imagesx($this->resource);
		$img_height=imagesy($this->resource);

		if ($width >= $img_width && $height >= $img_height) {
			return $this;
		}

		$this->thumb_width=$width;
		$this->thumb_height=$height;
		$thumb_ratio=$width/$height;

		// get image ratio
		if (($img_height*$width/$img_width) <= $height) {
			$scaled_width=round($img_height*$thumb_ratio);
			$scaled_height=$img_height;
			$src_x=$img_width/2-$scaled_width/2;
			$src_y=0;
		}
		else {
			$scaled_width=$img_width;
			$scaled_height=round($img_width/$thumb_ratio);
			$src_x=0;
			$src_y=$img_height/2-$scaled_height/2;
		}

		$tmp=$this->createImage($scaled_width,$scaled_height);
		$this->im($tmp,$this->resource,0,0,$src_x,$src_y,$scaled_width,$scaled_height,$scaled_width,$scaled_height);
		$this->thumb=$this->createImage($width,$height);
		$this->im($this->thumb,$tmp,0,0,0,0,$width,$height,$scaled_width,$scaled_height);

		return $this;
	}

	/**
	 * Round thumb corners using antialiasing.
	 *
	 * @param int $corner_radius Corner radius
	 * @param array $color RGB color of background
	 * @param int $corners Corners to round
	 * @return Thumbnailer
	 * @since 1.1.0
	 * @magic premium
	 * @throws ThumbnailerException
	 * @access public
	 */
	public function &round($corner_radius=5, $color=array(255,255,255), $corners=Thumbnailer::ALL)
	{
		$tyl=$this->createImage($this->thumb_width, $this->thumb_height);
		imagecopy($tyl, $this->getResource(), 0, 0, 0, 0, $this->thumb_width, $this->thumb_height);
		$startx=$this->thumb_width*2-1;
		$starty=$this->thumb_height*2-1;
		$im_temp=$this->createImage($startx,$starty);
		imagecopyresampled($im_temp, $tyl, 0, 0, 0, 0, $startx, $starty, $this->thumb_width, $this->thumb_height);

		$bg=imagecolorallocate($im_temp, $color[0], $color[1], $color[2]);
		$startsize=$corner_radius*3-1;
		$arcsize=$startsize*2+1;

		if ($corners & self::TOP_LEFT) {
			imagearc($im_temp, $startsize, $startsize, $arcsize, $arcsize, 180,270,$bg);
			imagefilltoborder($im_temp,0,0,$bg,$bg);
		}
		if ($corners & self::TOP_RIGHT) {
			imagearc($im_temp, $startx-$startsize, $startsize,$arcsize, $arcsize, 270,360,$bg);
			imagefilltoborder($im_temp,$startx,0,$bg,$bg);
		}
		if ($corners & self::BOTTOM_LEFT) {
			imagearc($im_temp, $startsize, $starty-$startsize,$arcsize, $arcsize, 90,180,$bg);
			imagefilltoborder($im_temp,0,$starty,$bg,$bg);
		}
		if ($corners & self::BOTTOM_RIGHT) {
			imagearc($im_temp, $startx-$startsize, $starty-$startsize,$arcsize, $arcsize, 0,90,$bg);
			imagefilltoborder($im_temp,$startx,$starty,$bg,$bg);
		}

		imagecopyresampled($this->getResource(), $im_temp, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $startx, $starty);

		imagecolordeallocate($im_temp, $bg);
		imagedestroy($im_temp);
		imagedestroy($tyl);

		return $this;
	}

	/**
	 * Color helper.
	 * For example black is Thumbnailer::colorRGB(0,0,0)
	 *
	 * @param int $r Red color 0-255
	 * @param int $g Green color 0-255
	 * @param int $b Blue color 0-255
	 * @return array
	 * @since 1.1.1
	 * @access public
	 * @magic premium
	 */
	public static function colorRGB($r,$g,$b)
	{
		return array($r,$g,$b);
	}

	/**
	 * Hex color helper.
	 * For example black is Thumbnailer::colorHex('#000000')
	 *
	 * @param string $hex Color in hex value
	 * @return array
	 * @see colorRGB()
	 * @since 1.1.1
	 * @access public
	 * @magic premium
	 */
	public static function colorHex($hex)
	{
		return self::colorRGB(hexdec(substr($hex,1,2)),hexdec(substr($hex,3,2)),hexdec(substr($hex,5,2)));
	}

	/**
	 * Saves created the result.
	 * If $file is not specified file will be flushed into the browser. If so use {@link header()} helper to send proper headers.
	 * Quality setting is used only for JPEG images. PNG ignores this completely.
	 *
	 * @param string $file Destination file for the thumbnail
	 * @param int $quality Quality of JPEG image (default 85)
	 * @return mixed False on error, filename on success
	 * @since 1.0.0
	 * @access public
	 */
	public function save($file=null, $quality=85)
	{
		$saveFunctions=array(
			'jpg'=>'imagejpeg',
			'jpeg'=>'imagejpeg',
			'jpe'=>'imagejpeg',
			'png'=>'imagepng',
			'x-png'=>'imagepng',
			'gif'=>'imagegif',
		);

		// whether flushing directly to the browser or not
		// determine the name of the open function
		$ext=$file===null ? $this->filename : $file;
        $ext=substr($ext, strrpos($ext, '.')+1);
		$function=$file===null ? $saveFunctions[image_type_to_extension($this->mime, false)] : $saveFunctions[$ext];

		// fix image quality to png value if necessary
		// phpgd ignores quality value for pngs ffs so skip it
		$quality=$ext=='png' ? null : $quality;

        // save file, update the object
        if (($filename=$function($this->getResource(), $file, $quality)) !== false) {
            $this->file=$file;
            $this->filename=basename($file);
            return $file;
        }

        return false;
	}

	/**
	 * Resource getter.
	 *
	 * @return resource
	 * @since 1.3.3
	 * @access public
	 */
	public function getResource()
	{
		return empty($this->thumb) ? $this->resource : $this->thumb;
	}

	/**
	 * Creates internal php image (preserves png/gif transparency).
	 *
	 * @param int $width Image width
	 * @param int $height Image height
	 * @return resource
	 * @since 1.3.0
	 * @access private
	 */
	protected function createImage($width, $height)
	{
		$thumb=imagecreatetruecolor($width, $height);

		if ($this->mime == IMAGETYPE_PNG || $this->mime == IMAGETYPE_GIF)
		{
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
			$transparent=imagecolorallocatealpha($thumb, 255, 255, 255, 127);
			imagefilledrectangle($thumb, 0, 0, $width, $width, $transparent);
			imagecolordeallocate($thumb, $transparent);
		}

		return $thumb;
	}

	/**
	 * Resample image more quickly.
	 *
	 * @param resource $dim
	 * @param resource $sim
	 * @param int $dx
	 * @param int $dy
	 * @param int $sx
	 * @param int $sy
	 * @param int $dw
	 * @param int $dh
	 * @param int $sw
	 * @param int $sh
	 * @param int $q
	 * @see imagecopyresampled()
	 * @since 1.0.0
	 * @access private
	 */
	public function im(&$dim,$sim,$dx,$dy,$sx,$sy,$dw,$dh,$sw,$sh,$q=3)
	{
		if ((($dw*$q)<$sw||($dh*$q)<$sh)&&$q<5){
			$t=$this->createImage($dw*$q+1,$dh*$q+1);
			@imagecopyresampled($t,$sim,0,0,$sx,$sy,$dw*$q+1,$dh*$q+1,$sw,$sh);
			@imagecopyresampled($dim,$t,$dx,$dy,0,0,$dw,$dh,$dw*$q,$dh*$q);
			@imagedestroy($t);
		}
		else {
			@imagecopyresampled($dim,$sim,$dx,$dy,$sx,$sy,$dw,$dh,$sw,$sh);
		}
	}

	/**
	 * Sets allowed mime types.
	 *
	 * @param array $allowed Allowed mime types
	 * @static
	 * @since 1.0.0
	 * @access public
	 */
	public static function setAllowed($allowed)
	{
		if (!is_array($allowed)) {
			$allowed=array($allowed);
		}

		self::$allowed=$allowed;
	}

	/**
	 * Grayscale helper.
	 * Turns image grayscale.
	 * This method can be chained.
	 *
	 * @return Thumbnailer
	 * @throws ThumbnailerException
	 * @since 1.2.5
	 * @magic premium
	 * @access public
	 */
	public function &effectGray()
	{
		for ($x=0; $x<$this->thumb_width; $x++)
		{
			for ($y=0; $y<$this->thumb_height; $y++)
			{
				$rgb=imagecolorat($this->getResource(), $x, $y);
				$rr=($rgb >> 16) & 0xFF;
                $gg=($rgb >> 8) & 0xFF;
                $bb=$rgb & 0xFF;

                $gray=round(($rr + $gg + $bb) / 3);
                $color=imagecolorallocate($this->getResource(), $gray, $gray, $gray);
                imagesetpixel($this->getResource(), $x, $y, $color);
                imagecolordeallocate($this->getResource(), $color);
			}
		}

		return $this;
	}

	/**
	 * Apply custom effect on the image.
	 *
	 * Example usage:
	 * <pre>
	 * // this code will apply gaussian blur on the thumb
	 * $thumbObject
	 *	->thumbSquare(200,200)
	 *	->effectCustom(array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0)))
	 *	->save('output_with_blur.jpg');
	 * </pre>
	 *
	 * @magic premium
	 * @access public
	 * @throws ThumbnailerException
	 * @see http://www.php.net/imageconvolution
	 * @param array $matrix A 3x3 matrix: an array of three arrays of three floats
	 * @param float $offset	Color offset
	 * @return Thumbnailer
	 * @since 1.3.5
	 */
	public function &effectCustom($matrix, $offset=0)
	{
		if (!function_exists('imageconvolution')) {
			throw new ThumbnailerException('Imageconvolution() function not available', 13);
		}

		imageconvolution($this->getResource(), $matrix, array_sum(array_map('array_sum', $matrix)), $offset);
		return $this;
	}

	/**
	 * Helper sends headers to display thumb directly into the browser.
	 * This method can be chained.
	 *
	 * @since 1.3.2
	 * @magic premium
	 * @access public
	 * @return Thumbnailer
	 */
	public function header()
	{
		if (error_get_last() == null) {
			header('Content-type: '.image_type_to_mime_type($this->mime));
			return $this;
		}
	}

	/**
	 * Helper generates random name for the thumbnail.
	 *
	 * Example usage:
	 * <pre>
	 * $th=Thumbnailer::create('image.jpg');
	 *
	 * $fileName=$th->randomName();
	 * $th->round()->save($fileName);
	 * echo 'File was saved as '.$filename;
	 * </pre>
	 *
	 * @since 1.3.2
	 * @magic premium
	 * @param int $length Name length
	 * @return string Random name
	 * @access public
	 */
	public function randomName($length=10)
	{
        $ext=strtolower(image_type_to_extension($this->mime));
        $ext=$ext=='.jpeg' ? '.jpg' : $ext;
		return substr(md5(uniqid(mt_rand()*1000000, true)), 0, $length).$ext;
	}

	/**
	 * Set text align for {@link logoText()} or {@link logoPhoto()} watermark.
	 * Correct values are Thumbnailer::TOP_LEFT, Thumbnailer::TOP_RIGHT, Thumbnailer::BOTTOM_LEFT, Thumbnailer::BOTTOM_RIGHT and Thumbnailer::CENTER.
	 * This method can be chained.
	 *
	 * @param int $align
	 * @return Thumbnailer
	 * @magic premium
	 * @since 1.3.3
	 * @access public
	 */
	public function logoAlign($align)
	{
		$this->logoAlign=$align;
		return $this;
	}

	/**
	 * Puts text based watermark on the thumb at $x and $y position from the corner set by {@link logoAlign()}. Default corner is bottom left.
	 * $color should be an array of (R,G,B) or use {@link colorHex()}, {@link colorRGB()} helpers.
	 * $font - path to the TrueType font
	 * This method can be chained.
	 *
	 * @param string $text
	 * @param int $size
	 * @param int $x
	 * @param int $y
	 * @param array $color
	 * @param string $font
	 * @return Thumbnailer
	 * @throws ThumbnailerException
	 * @since 1.2.4
	 * @access public
	 * @magic premium
	 */
	public function logoText($text, $font, $size=11, $x=10, $y=10, $color=array(255,255,255))
	{
		$_color=imagecolorallocate($this->getResource(), $color[0], $color[1], $color[2]);
		@imagealphablending($this->getResource(), true);

		// calculate bounding box
		$bbox=imagettfbbox($size, 0, $font, $text);
		$width=$bbox[2]-$bbox[0];
		$height=$bbox[1]-$bbox[7];

		if ($this->logoAlign == self::TOP_LEFT) {
			$_x=$x;
			$_y=$height+$y;
		}
		elseif ($this->logoAlign == self::TOP_RIGHT) {
			$_x=$this->thumb_width-$width-$x;
			$_y=$height+$y;
		}
		elseif ($this->logoAlign == self::BOTTOM_LEFT) {
			$_x=$x;
			$_y=$this->thumb_height-$y;
		}
		elseif ($this->logoAlign == self::BOTTOM_RIGHT) {
			$_x=$this->thumb_width-$width-$x;
			$_y=$this->thumb_height-$y;
		}
		elseif ($this->logoAlign == self::CENTER) {
			$_x=floor(($this->thumb_width-$width)/2);
			$_y=floor($this->thumb_height/2 + $height/2);
		}

		imagettftext($this->getResource(), $size, 0, $_x, $_y, $_color, $font, $text);
		imagecolordeallocate($this->getResource(), $_color);
		return $this;
	}

	/**
	 * Puts image watermark on the thumb at $x and $y position from the corner set by {@link logoAlign()}. Default corner is bottom left.
	 * Alpha should be between 0 and 100. Use it only with PNG images though.
	 * This method can be chained.
	 *
	 * @param string $photo
	 * @param int $x
	 * @param int $y
	 * @param int $alpha
	 * @throws ThumbnailerException
	 * @since 1.2.4
	 * @return Thumbnailer
	 * @access public
	 * @magic premium
	 */
	public function logoPhoto($photo, $x=10, $y=10, $alpha=100)
	{
		$th=new Thumbnailer($photo);
		$width=imagesx($th->getResource());
		$height=imagesy($th->getResource());

		if ($this->logoAlign == self::TOP_LEFT) {
			$_x=$x;
			$_y=$y;
		}
		elseif ($this->logoAlign == self::TOP_RIGHT) {
			$_x=$this->thumb_width-$width-$x;
			$_y=$y;
		}
		elseif ($this->logoAlign == self::BOTTOM_LEFT) {
			$_x=$x;
			$_y=$this->thumb_height-$height-$y;
		}
		elseif ($this->logoAlign == self::BOTTOM_RIGHT) {
			$_x=$this->thumb_width-$width-$x;
			$_y=$this->thumb_height-$height-$y;
		}
		elseif ($this->logoAlign == self::CENTER) {
			$_x=floor(($this->thumb_width-$width)/2);
			$_y=floor($this->thumb_height/2 - $height/2);
		}

		imagecopymerge($this->getResource(), $th->getResource(), $_x, $_y, 0, 0, $width, $height, $alpha);
		unset($th);
		return $this;
	}

	/**
	 * Batch mode helper.
	 * For more information about the input mask
	 * please refer to php's <a href="http://php.net/glob">glob</a> function manual.
	 *
	 * Example usage:
	 * <pre>
	 * function callb(Thumbnailer $th) {
	 * 	$filename=$th->randomName();
	 * 	$th->thumbFixed(120,90)->round()->save('/save/path'.$filename);
	 * 	return $filename;
	 * }
	 *
	 * Thumbnailer::batch('callb', '/read/path/*.jpg');
	 * </pre>
	 *
	 * @param string $in Read from directory.
	 * @param function $callback Callback function
	 * @throws ThumbnailerException
	 * @return array User output
	 * @magic premium
	 * @since 1.2.1
	 * @access public
	 */
	public static function &batch($callback, $in)
	{
		if (!function_exists($callback)) {
			throw new ThumbnailerException(sprintf('Callback function %s is not defined', $callback), 10);
		}

		$return=array();
		foreach(glob($in,GLOB_BRACE) as $img)
		{
			$th=new Thumbnailer($img);
			$return[]=$callback($th);
			unset($th);
		}

		return $return;
	}

	/**
	 * Upload helper.
	 * Example usage:
	 * <pre>
	 * function callb(Thumbnailer $th) {
	 *    return $th->round()->save('/save/path/'.$th->randonName());
	 * }
	 *
	 * echo Thumbnailer::upload('file', 'callb');
	 * </pre>
	 *
	 * @param string $fieldName Field name in $_FILES superglobal.
	 * @param callback $callback Callback function name
	 * @return mixed Output
	 * @throws ThumbnailerException
	 * @magic premium
	 * @access public
	 */
	public static function upload($fieldName, $callback)
	{
		if (!function_exists($callback)) {
			throw new ThumbnailerException(sprintf('Callback function %s is not defined', $callback), 10);
		}

        $errors=array(
            UPLOAD_ERR_FORM_SIZE=>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL=>'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE=>'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR=>'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE=>'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION=>'A PHP extension stopped the file upload',
        );

        if ($_FILES[$fieldName]['error'] != 0) {
            throw new ThumbnailerException(sprintf('There was an error in your upload: %s', $errors[$_FILES[$fieldName][$error]]), 11);
        }

        $thumb=new Thumbnailer($_FILES[$fieldName]['tmp_name']);
		return $callback($thumb);
	}

	/**
	 * Retrieve EXIF data from the image.
	 *
	 * Example usage:
	 * <pre>
	 * print_r($thumbnailerObject->readExif());
	 * </pre>
	 *
	 * @magic premium
	 * @throws ThumbnailerException
	 * @see http://www.php.net/exif_read_data
	 * @param string $sections Comma separated list of sections that need to be present in file to produce a result array
	 * @return array
	 * @since 1.3.5
	 * @access public
	 */
	public function readExif($sections=null)
	{
		if (!function_exists('exif_read_data')) {
			throw new ThumbnailerException('EXIF extension not loaded', 12);
		}

		return exif_read_data($this->file, $sections);
	}
}