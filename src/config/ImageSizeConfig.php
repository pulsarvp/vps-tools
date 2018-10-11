<?php

	namespace vps\tools\config;

	use yii\base\InvalidCallException;
	use yii\base\UnknownPropertyException;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 */
	class ImageSizeConfig
	{

		public static $params = [
			'HD' => [ 'width' => 500, 'height' => 500, 'quality' => 80 ],
			'SD' => [ 'width' => 150, 'height' => 150, 'quality' => 80 ]
		];
		public static $fit    = ImageSizeFit::NONE;

		/**
		 * ImageSizeConfig constructor.
		 * @param     $name
		 * @param     $width
		 * @param     $height
		 * @param int $quality
		 */
		public function __construct ($name = 'HD', $width = 500, $height = 500, $quality = 90)
		{
			if (isset(self::$params[ $name ]))
			{
				self::$params[ $name ] = [ 'width' => $width, 'height' => $height, 'quality' => 90 ];
			}
		}

		/**
		 * @param $name
		 * @return mixed
		 * @throws UnknownPropertyException
		 */
		public function get ($name)
		{

			if (isset(self::$params[ $name ]))
			{
				return self::$params[ $name ];
			}

			return null;
		}
	}