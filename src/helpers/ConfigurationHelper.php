<?php
	namespace vps\tools\helpers;

	use Yii;
	use yii\base\InvalidConfigException;
	use yii\base\Object;
	use yii\i18n\PhpMessageSource;

	/**
	 * Class HumanHelper
	 *
	 * @package vps\tools\helpers
	 */
	class ConfigurationHelper
	{
		/**
		 * Adds i18n app configuration. If configuration with given name exists filemap is populated.
		 * ```php
		 * ConfigurationHelper::addTranslation('setting', [ 'setting' => 'setting.php' ]);
		 * // In that case if 'setting' i18n configuration exists, filemap will be merged with new one.
		 * // If not - exception will be raised since $basePath is not a directory.
		 * ```
		 *
		 * @param string      $name
		 * @param array       $fileMap
		 * @param string|null $basePath
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function addTranslation ($name, $fileMap, $basePath = null)
		{
			$name .= '*';
			if (isset(Yii::$app->i18n->translations[ $name ]))
			{
				Yii::$app->i18n->translations[ $name ][ 'fileMap' ] = array_merge(
					Yii::$app->i18n->translations[ $name ][ 'fileMap' ],
					$fileMap
				);
			}
			else
			{
				if (!is_dir($basePath))
					throw new InvalidConfigException("Directory does not exist: " . $basePath);

				Yii::$app->i18n->translations[ $name ] = new PhpMessageSource([
					'basePath'         => $basePath,
					'forceTranslation' => true,
					'sourceLanguage'   => 'en',
					'fileMap'          => $fileMap
				]);
			}
		}
	}