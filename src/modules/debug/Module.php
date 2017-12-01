<?php

	namespace vps\tools\modules\debug;

	use Yii;

	class Module extends \yii\debug\Module
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

			parent::renderToolbar($event);
		}

		public function getViewPath ()
		{
			return Yii::getAlias('@vendor') . '/yiisoft/yii2-debug/views';
		}
	}
