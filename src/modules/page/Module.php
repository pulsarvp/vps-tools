<?php

	namespace vps\tools\modules\page;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *```php
	 * 'pages'   => [
	 * 'class'         => 'vps\tools\modules\page\Module',
	 * 'useMenu'       => false,
	 * 'modelMenu'     => 'vps\tools\modules\menu\models\Menu',
	 * 'modelMenuType' => 'vps\tools\modules\menu\models\MenuType'
	 * ],
	 * ```
	 * @package vps\tools\modules\page
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\page\controllers';
		/**
		 * @var boolean view functional menu
		 */
		public $useMenu = false;
		/**
		 * @var string the class menu
		 */
		public $modelMenu = 'vps\tools\modules\menu\models\Menu';
		/**
		 * @var string the class menu type
		 */
		public $modelMenuType = 'vps\tools\modules\menu\models\MenuType';
		public $title         = "Manage page";

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@pageViews' => __DIR__ . '/views' ]);
			$app->setAliases([ '@vpsViews' => __DIR__ . '/../../views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/add',
				  'route'   => $this->id . '/page/add'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/edit/<id:[\w\-]+>',
				  'route'   => $this->id . '/page/edit'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/delete',
				  'route'   => $this->id . '/page/delete'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/activate',
				  'route'   => $this->id . '/page/activate'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/view/<id:[\w\-]+>',
				  'route'   => $this->id . '/page/view'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'page/<id:[\w\-]+>',
				  'route'   => $this->id . '/page/frontend'
				],
			], true);
			ConfigurationHelper::addTranslation('page', [ 'page' => 'page.php' ], __DIR__ . '/messages');
			$this->title = Yii::tr($this->title, [], 'page');
		}
	}