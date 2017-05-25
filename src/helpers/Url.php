<?php
	namespace vps\tools\helpers;

	/**
	 * Class Url
	 * @package vps\tools\helpers
	 */
	class Url extends \yii\helpers\Url
	{
		/**
		 * Immediate redirect to given URL.
		 *
		 * ```php
		 * Url::redirect(Url::to(['site/index']));
		 * ```
		 * @param string $url
		 */
		public static function redirect ($url)
		{
			header('Location: ' . $url);
			exit();
		}
	}
