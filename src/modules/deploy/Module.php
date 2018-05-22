<?php

	namespace vps\tools\modules\deploy;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *```php
	 * 'deploy'   => [
	 * 'class'         => 'vps\tools\modules\deploy\Module'
	 * ],
	 * ```
	 * @package vps\tools\modules\deploy
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\deploy\controllers';

		public $img = 'http://pulsarvp.ru/images/_tech.jpg';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@deployViews' => __DIR__ . '/views' ]);
			$app->setAliases([ '@vpsViews' => __DIR__ . '/../../views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'env',
				  'route'   => $this->id . '/env/deploy'
				]
			], false);
			ConfigurationHelper::addTranslation('deploy', [ 'deploy' => 'deploy.php' ], __DIR__ . '/messages');
		}
	}