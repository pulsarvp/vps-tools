<?php

	namespace vps\tools\modules\menu;

	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *
	 * @package vps\tools\modules\menu
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\menu\controllers';

		public $title = "Manage menu";

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@menuViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'menu/add',
				  'route'   => $this->id . '/menu/add'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'menu/edit',
				  'route'   => $this->id . '/menu/edit'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'menu/delete',
				  'route'   => $this->id . '/menu/delete'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'menu/visible',
				  'route'   => $this->id . '/menu/visible'
				],
			], true);

			ConfigurationHelper::addTranslation('menu', [ 'menu' => 'menu.php' ], __DIR__ . '/messages');

			$this->title = Yii::tr($this->title, [], 'menu');
		}
	}