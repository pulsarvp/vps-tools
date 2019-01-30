<?php

	namespace vps\tools\modules\message;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2019
	 * @date      2019-01-30
	 */

	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *```php
	 * 'messages'   => [
	 * 'class'         => 'vps\tools\modules\message\Module',
	 * ],
	 * ```
	 * @package vps\tools\modules\message
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\message\controllers';
		public $languages           = [ 'en', 'ru' ];

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@messageViews' => __DIR__ . '/views' ]);
			$app->setAliases([ '@vpsViews' => __DIR__ . '/../../views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/index',
				  'route'   => $this->id . 'message/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/create',
				  'route'   => $this->id . '/message/create'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/update/<id:[\w\-]+>',
				  'route'   => $this->id . '/message/update'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/delete',
				  'route'   => $this->id . '/message/delete'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/view/<id:[\w\-]+>',
				  'route'   => $this->id . '/message/view'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'message/view',
				  'route'   => $this->id . '/message/view'
				],
			], true);
			ConfigurationHelper::addTranslation('message', [ 'message' => 'message.php' ], __DIR__ . '/messages');
		}

	}