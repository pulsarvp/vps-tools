<?php

	namespace vps\tools\modules\debug;

	use Yii;
    use yii\helpers\IpHelper;

    class Module extends \yii\debug\Module
	{

		public $allowedIPsDb   = 'debug_allowed_ips';
		public $allowedHostsDb = 'debug_allowed_hosts';

        public $disableCallbackRestrictionWarning = false;
        public $disableIpRestrictionWarning = false;
		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			$ips = Yii::$app->settings->get($this->allowedIPsDb);
			if (!is_null($ips))
				$this->allowedIPs = explode(',', $ips);

			$hosts = Yii::$app->settings->get($this->allowedHostsDb);
			if (!is_null($hosts))
				$this->allowedHosts = explode(',', $hosts);

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
			return Yii::getAlias('@vendor') . '/yiisoft/yii2-debug/src/views';
		}

	}
