<?php
	namespace vps\tools\modules\setting;

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

			// Add module I18N category.
			if (!isset($app->i18n->translations[ 'apiapp.*' ]))
			{
				Yii::$app->i18n->translations[ 'setting*' ] = [
					'class'            => 'yii\i18n\PhpMessageSource',
					'basePath'         => __DIR__ . '/messages',
					'forceTranslation' => true,
					'fileMap'          => [
						'setting' => 'setting.php',
					]
				];
			}

			$this->title = Yii::tr($this->title, [], 'setting');
		}
	}