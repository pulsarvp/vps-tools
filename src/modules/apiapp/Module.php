<?php
	namespace vps\tools\modules\apiapp;

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
		}
	}