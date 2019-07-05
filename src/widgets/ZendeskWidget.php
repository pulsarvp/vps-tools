<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-07-12
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use Yii;

	class ZendeskWidget extends AnalyticWidgetAbstract
	{
		public $nameSettingAnalyticUseSuffix = 'zendesk_use';
		public $nameSettingAnalyticKeySuffix = 'zendesk_key';

		public $user;

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if (Yii::$app->settings->get($this->getNameUseSetting()))
				return $this->renderFile(__DIR__ . '/views/analytics/zendesk.tpl', [
					'key'  => Yii::$app->settings->get($this->getNameKeySetting()),
					'user' => $this->user
				]);
		}

	}