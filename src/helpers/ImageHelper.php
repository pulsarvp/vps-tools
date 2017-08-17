<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-08-17
	 * @package   vps\tools\helpers
	 */

	namespace vps\tools\helpers;

	use Imagine\Image\Box;
	use Imagine\Image\Point;
	use Imagine\Imagick\Imagine;

	/**
	 * Class ImageHelper
	 * @package vps\tools\helpers
	 */
	class ImageHelper
	{

		/**
		 * Crop image to square.
		 * ```php
		 * $cropBoxData = '{"x":50,"y":0,"width":50,"height":50,"rotate":0,"scaleX":1,"scaleY":1}';
		 * $result = ImageHelper::cropSquare('/var/www/img.png','var/www/img_crop.png',$cropBoxData);
		 * // $result will be: true
		 * ```
		 *
		 * @param string $file
		 * @param string $filepath
		 * @param string $cropBoxData
		 *
		 * @return boolean
		 */
		public static function cropSquare ($file, $filepath, $cropBoxData = null)
		{

			if (!RemoteFileHelper::exists($file))
				return false;

			$imagine = new Imagine();
			$crop = $imagine->open($file);
			$size = $crop->getSize();

			if (!is_null($cropBoxData))
			{
				$coorImg = json_decode($cropBoxData);
				$coorImg->x = max($coorImg->x, 0);
				$coorImg->y = max($coorImg->y, 0);
				$coorImg->width = min($coorImg->width, $size->getWidth());
				$coorImg->height = min($coorImg->height, $size->getHeight());
				$crop->crop(new Point($coorImg->x, $coorImg->y), new Box($coorImg->width, $coorImg->height))->save($filepath);
			}
			else
			{
				$width = min($size->getHeight(), $size->getWidth());
				$centreX = round($size->getWidth() / 2);
				$centreY = round($size->getHeight() / 2);
				$cropWidthHalf = round($width / 2);
				$cropHeightHalf = round($width / 2);

				$x1 = max(0, $centreX - $cropWidthHalf);
				$y1 = max(0, $centreY - $cropHeightHalf);

				$x2 = min($width, $centreX + $cropWidthHalf);
				$y2 = min($width, $centreY + $cropHeightHalf);

				$crop->crop(new Point($x1, $y1), new Box($x2, $y2))->save($filepath);
			}

			return true;
		}
	}