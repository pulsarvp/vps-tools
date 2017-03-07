<?php
	namespace vps\tools\modules\apiapp;

	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module ApiApp
	 *
	 * @package vps\tools\modules\apiapp
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{

		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\apiapp\controllers';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@appViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'app/<action:[\w\-]+>',
				  'route'   => $this->id . '/apiapp/<action>'
				],
			], false);

			// Add module I18N category.
			if (!isset( $app->i18n->translations[ 'apiapp.*' ] ))
			{
				Yii::$app->i18n->translations[ 'apiapp*' ] = [
					'class'            => 'yii\i18n\PhpMessageSource',
					'basePath'         => __DIR__ . '/messages',
					'forceTranslation' => true,
					'fileMap'          => [
						'apiapp' => 'apiapp.php',
					]
				];
			}
		}
	}