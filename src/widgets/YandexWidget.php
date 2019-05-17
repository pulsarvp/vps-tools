<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-07-12
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use Yii;

	class YandexWidget extends AnalyticWidgetAbstract
	{
		public $nameSettingAnalyticUseSuffix = 'yandex_use';
		public $nameSettingAnalyticKeySuffix = 'yandex_key';
		public $userHash;

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if (Yii::$app->settings->get($this->getNameUseSetting()))
				return $this->renderFile(__DIR__ . '/views/analytics/yandex.tpl', [
					'key'  => Yii::$app->settings->get($this->getNameKeySetting()),
					'hash' => $this->userHash
				]);
		}

	}