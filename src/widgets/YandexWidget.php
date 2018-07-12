<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-07-12
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use Yii;
	use yii\base\Widget;
	use yii\web\View;

	class YandexWidget extends Widget
	{

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();
			$this->view = new View([
				'renderers' => [
					'tpl' => [
						'class'   => 'yii\smarty\ViewRenderer',
						'imports' => [
							'Html' => '\vps\tools\helpers\Html',
							'Url'  => '\vps\tools\helpers\Url'
						]
					]
				]
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if (Yii::$app->settings->get('analytics_yandex_use'))
				return $this->renderFile(__DIR__ . '/views/analytics/yandex.tpl', [
					'key' => Yii::$app->settings->get('analytics_yandex_key')
				]);
		}
	}