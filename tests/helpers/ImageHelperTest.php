<?php

	namespace tests\helpers;

	use Imagine\Imagick\Imagine;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\ImageHelper;

	class ImageHelperTest extends \PHPUnit\Framework\TestCase
	{
		private $_datapath = __DIR__ . '/../data/img';

		public function testCropSquare ()
		{
			FileHelper::createDirectory($this->_datapath);
			$imagine = new Imagine();
			$file = 'https://www.google.ru/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png';
			$cropBoxData = [ 'x' => '50', 'y' => '0', 'width' => '50', 'height' => '50' ];

			$this->assertFalse(ImageHelper::cropSquare('', $this->_datapath . '/img_2.png', $cropBoxData));

			$this->assertTrue(ImageHelper::cropSquare($file, $this->_datapath . '/img_1.png', $cropBoxData));

			$crop = $imagine->open($this->_datapath . '/img_1.png');
			$size = $crop->getSize();
			$this->assertTrue($size->getHeight() == $size->getWidth());

			$this->assertTrue(ImageHelper::cropSquare($file, $this->_datapath . '/img_2.png'));
			$crop = $imagine->open($this->_datapath . '/img_2.png');
			$size = $crop->getSize();
			$this->assertTrue($size->getHeight() == $size->getWidth());

			FileHelper::clearDir($this->_datapath);
			FileHelper::removeDirectory($this->_datapath);
		}

	}
