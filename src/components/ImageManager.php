<?php

	namespace vps\tools\components;

	use Imagine\Gd\Imagine;
	use Imagine\Image\Box;
	use Imagine\Image\BoxInterface;
	use Imagine\Image\ImageInterface;
	use vps\tools\config\ImageSizeConfig;
	use vps\tools\config\ImageSizeFit;
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
		public $config = null;

		public function init ($config = null)
		{
			parent::init();
			if ($config == null)
				$this->config = new ImageSizeConfig();
			else
				$this->config = $config;
		}

		/**
		 * Save all formats image.
		 *
		 * @param string       $path The path to the file.
		 * @param UploadedFile $file The path to the source file.
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
			if ($resize)
			{
				$data = [];
				foreach ($this->_formats() as $format)
				{
					$method = '_save' . ucfirst($format);
					$this->$method($path, $name, $file->tempName);
					$data[ $format ] = $path . DIRECTORY_SEPARATOR . $name;
				}
				if (file_exists($filepath))
					return $data;
				else
					return false;
			}
			else
			{
				$this->_saveOriginal($path, $name, $file->tempName);

				if (file_exists($filepath))
					return [ self::F_ORIGINAL => $path . DIRECTORY_SEPARATOR . $name ];
				else
					return false;
			}
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
			$class = $this->config;
			$params = $class::get($format);
			$newSize = $this->_size($format);
			if ($class::$fit == ImageSizeFit::WIDTH)
				$this->resizeToWidth($image, $newSize->getWidth());
			elseif ($class::$fit == ImageSizeFit::HEIGHT)
				$this->resizeToHeight($image, $newSize->getHeight());
			else
				$this->_resize($image, $newSize);
			FileHelper::createDirectory(dirname($path));
			$image->save($path, [ 'jpeg_quality' => $params[ 'quality' ] ]);
		}

		/**
		 * Resizes image according to the given height (width proportional)
		 *
		 * @param integer $height
		 * @param boolean $allow_enlarge
		 * @return static
		 */
		public function resizeToHeight ($image, $height)
		{
			$ratio = $height / $image->getSize()->getHeight();
			$width = $image->getSize()->getWidth() * $ratio;
			$this->_resize($image, new Box($width, $height));

			return $this;
		}

		/**
		 * Resizes image according to the given width (height proportional)
		 *
		 * @param integer $width
		 * @param boolean $allow_enlarge
		 * @return static
		 */
		public function resizeToWidth ($image, $width)
		{
			$ratio = $width / $image->getSize()->getWidth();
			$height = $image->getSize()->getHeight() * $ratio;
			$this->_resize($image, new Box($width, $height));

			return $this;
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
			$class = $this->config;
			$params = $class::get(strtoupper($format));

			return new Box(
				Yii::$app->settings->get('image_' . $format . '_width', $params[ 'width' ]),
				Yii::$app->settings->get('image_' . $format . '_height', $params[ 'height' ]));
		}
	}