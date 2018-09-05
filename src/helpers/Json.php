<?php

	namespace vps\tools\helpers;

	use Yii;

	/**
	 * Class Json
	 *
	 * @package vps\tools\helpers
	 */
	class Json extends \yii\helpers\BaseJson
	{
		public static function prettyPrint ($data)
		{
			if (is_string($data))
				$input = self::decode($data);
			else
				$input = $data;

			return self::encode($input, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		}
	}