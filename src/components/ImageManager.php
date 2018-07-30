<?php

	namespace vps\tools\components;

	use Imagine\Gd\Imagine;
	use Imagine\Image\Box;
	use Imagine\Image\BoxInterface;
	use Imagine\Image\ImageInterface;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\UuidHelper;
	use Yii;
	use yii\base\Component;
	use yii\web\UploadedFile;

	class ImageManager extends Component
	{
		const F_ORIGINAL = 'original';
		const F_SD       = 'sd';
		const F_HD       = 'hd';
		public $witdhSD = 150;
		public $witdhHD = 500;

		/**
		 * Save all formats image.
		 *
		 * @param string       $path   The path to the file.
		 * @param UploadedFile $file   The path to the source file.
		 * @param bool         $resize resize images.
		 *
		 * ```php
		 * return Yii::$app->image->saveImage('var/www/site/data/img/author', $image);
		 * ```
		 * @return bool|string
		 */
		public function saveImage ($path, $file, $resize = false)
		{
			$filename = UuidHelper::generate() . '.' . $file->extension;
			$name = $filename[ 0 ] . DIRECTORY_SEPARATOR . $filename[ 1 ] . DIRECTORY_SEPARATOR . $filename;
			$filepath = $path . DIRECTORY_SEPARATOR . $name;
			if ($this->resize)
			{
				foreach ($this->_formats() as $format)
				{
					$method = '_save' . ucfirst($format);
					$this->$method($path, $name, $file->tempName);
				}
			}
			else
			{
				$this->_saveOriginal($path, $name, $file->tempName);
			}

			if (file_exists($filepath))
				return $name;
			else
				return false;
		}

		/**
		 * @return string[] Names of allowed formats. _save{Format} method must exist.
		 */
		private function _formats ()
		{
			return [
				self::F_ORIGINAL,
				self::F_HD,
				self::F_SD
			];
		}

		/**
		 * Resize the image proportionally with max sizes constraints
		 *
		 * @param ImageInterface $image
		 * @param BoxInterface   $newSize
		 */
		private function _resize (ImageInterface $image, BoxInterface $newSize)
		{
			$size = $image->getSize();
			$ratio = min($newSize->getHeight() / $size->getHeight(), $newSize->getWidth() / $size->getWidth());
			if ($ratio < 1)
				$image->resize($size->scale($ratio));
		}

		/**
		 * Saves image to the format using sizes from settings.
		 *
		 * @param string $path
		 * @param string $dest
		 * @param string $type
		 * @param string $format
		 */
		private function _save ($path, $file, $format)
		{

			$image = ( new Imagine() )->open($file);

			$newSize = $this->_size($format);
			$this->_resize($image, $newSize);
			FileHelper::createDirectory(dirname($path));
			$image->save($path, [ 'jpeg_quality' => 80 ]);
		}

		/**
		 * Saves a image to the HD format.
		 *
		 * @param string $path
		 * @param string $dest
		 * @param string $type
		 */
		private function _saveHd ($path, $name, $file)
		{
			$this->_save($path . DIRECTORY_SEPARATOR . self::F_HD . DIRECTORY_SEPARATOR . $name, $file, self::F_HD);
		}

		/**
		 * Saves a image to the original format.
		 *
		 * @param string $path
		 * @param string $dest
		 * @param string $type
		 */
		private function _saveOriginal ($path, $name, $file)
		{
			$filepath = $path . DIRECTORY_SEPARATOR . $name;
			if (Yii::$app->settings->get('image_original_save', true))
			{
				FileHelper::createDirectory(dirname($filepath));
				copy($file, $filepath);
			}
		}

		/**
		 * Saves a image to the SD format.
		 *
		 * @param string $path
		 * @param string $dest
		 * @param string $type
		 */
		private function _saveSd ($path, $name, $file)
		{
			$this->_save($path . DIRECTORY_SEPARATOR . self::F_SD . DIRECTORY_SEPARATOR . $name, $file, self::F_SD);
		}

		/**
		 * @param string $type
		 * @param string $format
		 * @return BoxInterface Size of a image for the type and the format.
		 */
		private function _size ($format)
		{
			$name = 'widht' . strtoupper($format);

			return new Box(
				Yii::$app->settings->get('image_' . $format . '_width', $$name),
				Yii::$app->settings->get('image_' . $format . '_height', $$name));
		}
	}