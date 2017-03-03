<?php
	namespace vps\tools\modules\app;

	use yii\base\BootstrapInterface;

	/**
	 * Class Module ApiApp
	 *
	 * @package vps\tools\modules\app
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var array the permission in the module
		 */
		public $permission = [ 'view', 'edit', 'create', 'delete' ];
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\app\controllers';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@appViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'app/<controller:[\w\-]+>/<action:[\w\-]+>',
				  'route'   => 'app/<controller>/<action>'
				],
			], false);
		}
	}