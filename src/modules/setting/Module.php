<?php
	namespace vps\tools\modules\setting;

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;
	use Yii;

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
			$app->setAliases([ '@settingViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'setting/edit',
				  'route'   => $this->id . '/setting/edit'
				],
			], false);

			ConfigurationHelper::addTranslation('setting', [ 'setting' => 'setting.php' ], __DIR__ . '/messages');
		}

		public function getTitle ()
		{
			return Yii::tr("Manage settings", [], 'setting');
		}
	}