<?php

	namespace vps\tools\modules\version;

	use yii\base\BootstrapInterface;

	/**
	 * Class Module Version
	 *
	 * @package vps\tools\modules\version
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'version',
				  'route'   => $this->id . '/version/index'
				],
			]);
		}
	}