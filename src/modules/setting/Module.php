<?php
	namespace vps\tools\modules\setting;

	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *
	 * @package vps\tools\modules\setting
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\setting\controllers';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->getUrlManager()->addRules([
				[ 'class' => 'yii\web\UrlRule', 'pattern' => 'setting/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => 'setting/<controller>/<action>' ],
			], false);
		}
	}