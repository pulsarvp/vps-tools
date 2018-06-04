<?php

	namespace vps\tools\modules\log;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-05-24
	 */
	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *```php
	 * 'log'   => [
	 * 'class'         => 'vps\tools\modules\log\Module'
	 * ],
	 * ```
	 * @package vps\tools\modules\log
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\log\controllers';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@logViews' => __DIR__ . '/views' ]);
			$app->setAliases([ '@vpsViews' => __DIR__ . '/../../views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'log/index',
				  'route'   => $this->id . '/log/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'log',
				  'route'   => $this->id . '/log/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'log/json',
				  'route'   => $this->id . '/log/json'
				]
			], false);
			ConfigurationHelper::addTranslation('log', [ 'log' => 'log.php' ], __DIR__ . '/messages');
		}
	}