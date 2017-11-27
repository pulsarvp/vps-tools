<?php

	namespace vps\tools\debug;

	use Yii;
	use yii\helpers\Json;
	use yii\web\View;

	class DebugDb extends \yii\debug\Module
	{

		public $allowedIPsDB   = 'debug_allowed_ips';
		public $allowedHostsDB = 'debug_allowed_hosts';

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			if (!is_null(Yii::$app->settings->get($this->allowedIPsDB)))
				$this->allowedIPs = explode(',', Yii::$app->settings->get($this->allowedIPsDB));
			if (!is_null(Yii::$app->settings->get($this->allowedHostsDB)))
				$this->allowedHosts = explode(',', Yii::$app->settings->get($this->allowedIPsDB));
			parent::init();
		}

		public function renderToolbar ($event)
		{
			if (!$this->checkAccess() || Yii::$app->getRequest()->getIsAjax())
			{
				return;
			}

			/* @var $view View */
			$view = $event->sender;
			echo $view->renderDynamic('return Yii::$app->getModule("' . $this->id . '")->getToolbarHtml();');

			// echo is used in order to support cases where asset manager is not available
			echo '<style>' . $view->renderPhpFile(Yii::getAlias('@vendor') . '/yiisoft/yii2-debug/assets/toolbar.css') . '</style>';
			echo '<script>' . $view->renderPhpFile(Yii::getAlias('@vendor') . '/yiisoft/yii2-debug/assets/toolbar.js') . '</script>';
		}

		protected function defaultVersion ()
		{
			$packageInfo = Json::decode(file_get_contents(Yii::getAlias('@vendor') . '/yiisoft/yii2-debug/composer.json'));
			$extensionName = $packageInfo[ 'name' ];
			if (isset(Yii::$app->extensions[ $extensionName ]))
			{
				return Yii::$app->extensions[ $extensionName ][ 'version' ];
			}

			return parent::defaultVersion();
		}
	}
