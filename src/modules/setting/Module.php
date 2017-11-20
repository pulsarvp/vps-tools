<?php

	namespace vps\tools\modules\setting;

	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
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

		public $title = "Manage settings";

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
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'setting/value',
				  'route'   => $this->id . '/setting/value'
				],
			], false);

			ConfigurationHelper::addTranslation('setting', [ 'setting' => 'setting.php' ], __DIR__ . '/messages');

			$this->title = Yii::tr($this->title, [], 'setting');
		}
	}