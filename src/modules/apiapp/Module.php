<?php
	namespace vps\tools\modules\apiapp;

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;
	use Yii;

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

		public $title = "Manage API applications";

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
			ConfigurationHelper::addTranslation('apiapp', [ 'apiapp' => 'apiapp.php' ], __DIR__ . '/messages');

			$this->title = Yii::tr($this->title, [], 'apiapp');
		}
	}