<?php

	namespace vps\tools\modules\export;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 * @date      2018-06-27
	 */

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *```php
	 * 'export'   => [
	 * 'class'         => 'vps\tools\modules\export\Module'
	 * ],
	 * ```
	 * @package vps\tools\modules\export
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\export\controllers';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@exportViews' => __DIR__ . '/views' ]);
			$app->setAliases([ '@vpsViews' => __DIR__ . '/../../views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export/index',
				  'route'   => $this->id . '/export/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export',
				  'route'   => $this->id . '/export/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export/create',
				  'route'   => $this->id . '/export/create'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export/edit',
				  'route'   => $this->id . '/export/edit'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export/view',
				  'route'   => $this->id . '/export/view'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'export/generate',
				  'route'   => $this->id . '/export/generate'
				]
			], false);
			ConfigurationHelper::addTranslation('export', [ 'export' => 'export.php' ], __DIR__ . '/messages');
		}
	}