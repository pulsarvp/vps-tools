<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-07-12
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use Yii;

	class GoogleWidget extends AnalyticWidgetAbstract
	{
		public $nameSettingAnalyticUseSuffix = 'google_use';
		public $nameSettingAnalyticKeySuffix = 'google_key';
		public $userHash;

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if (Yii::$app->settings->get($this->getNameUseSetting()))
				return $this->renderFile(__DIR__ . '/views/analytics/google.tpl', [
					'key'  => Yii::$app->settings->get($this->getNameKeySetting()),
					'hash' => $this->userHash
				]);
		}
	}