<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Image;

/**
 * Misc functions to manipulate files
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 *
 */

class ImageTools {

	/**
	 * @param string $sourceFile
	 * @param string $target full path where to store
	 * @param integer $newWidth width
	 * @param integer $quality jpg compression
	 * @return bool
	 */
	public function createThumb($sourceFile, $target, $newWidth, $quality) {

		switch(@exif_imagetype($sourceFile)) {
			case IMAGETYPE_JPEG2000 :
			case IMAGETYPE_JPEG : $img = imagecreatefromjpeg($sourceFile); break;
			case IMAGETYPE_PNG : $img = imagecreatefrompng($sourceFile); break;
			case IMAGETYPE_GIF : $img = imagecreatefromgif($sourceFile); break;
			case IMAGETYPE_BMP : $img = imagecreatefromwbmp($sourceFile); break;
			default: $img = false;
		}

		if($img) {
			$width = imagesx($img);
			$height = imagesy($img);
			$newHeight = floor($height * $newWidth / $width);
			$tmpImg = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			imageinterlace($tmpImg, true);
			imagejpeg($tmpImg, $target, $quality);
			imagedestroy($tmpImg);
			imagedestroy($img);
			return true;
		} else return false;
	}

	/**
	 * @param $file
	 * @param $destination
	 * @param $w
	 * @param $h
	 */
	public static function resizeImage($file, $destination, $w, $h) {

		//Get the original image dimensions + type
		list($source_width, $source_height, $source_type) = getimagesize($file);

		//Figure out if we need to create a new JPG, PNG or GIF
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if ($ext == "jpg" || $ext == "jpeg") {
			$source_gdim=imagecreatefromjpeg($file);
		} elseif ($ext == "png") {
			$source_gdim=imagecreatefrompng($file);
		} elseif ($ext == "gif") {
			$source_gdim=imagecreatefromgif($file);
		} else {
			//Invalid file type? Return.
			return;
		}

		//If a width is supplied, but height is false, then we need to resize by width instead of cropping
		if ($w && !$h) {
			$ratio = $w / $source_width;
			$temp_width = $w;
			$temp_height = $source_height * $ratio;

			$desired_gdim = imagecreatetruecolor($temp_width, $temp_height);
			imagecopyresampled(
				$desired_gdim,
				$source_gdim,
				0, 0,
				0, 0,
				$temp_width, $temp_height,
				$source_width, $source_height
			);
		} else {
			$source_aspect_ratio = $source_width / $source_height;
			$desired_aspect_ratio = $w / $h;

			if ($source_aspect_ratio > $desired_aspect_ratio) {
				/*
                 * Triggered when source image is wider
                 */
				$temp_height = $h;
				$temp_width = ( int ) ($h * $source_aspect_ratio);
			} else {
				/*
                 * Triggered otherwise (i.e. source image is similar or taller)
                 */
				$temp_width = $w;
				$temp_height = ( int ) ($w / $source_aspect_ratio);
			}

			/*
             * Resize the image into a temporary GD image
             */

			$temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
			imagecopyresampled(
				$temp_gdim,
				$source_gdim,
				0, 0,
				0, 0,
				$temp_width, $temp_height,
				$source_width, $source_height
			);

			/*
             * Copy cropped region from temporary image into the desired GD image
             */

			$x0 = ($temp_width - $w) / 2;
			$y0 = ($temp_height - $h) / 2;
			$desired_gdim = imagecreatetruecolor($w, $h);
			imagecopy(
				$desired_gdim,
				$temp_gdim,
				0, 0,
				$x0, $y0,
				$w, $h
			);
		}

		/*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

		if ($ext == "jpg" || $ext == "jpeg") {
			ImageJpeg($desired_gdim,$destination,100);
		} elseif ($ext == "png") {
			ImagePng($desired_gdim,$destination);
		} elseif ($ext == "gif") {
			ImageGif($desired_gdim,$destination);
		} else {
			return;
		}

		ImageDestroy ($desired_gdim);
	}
}