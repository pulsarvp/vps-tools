<?php
	namespace vps\tools\modules\setting\widgets;

	use vps\tools\modules\setting\models\Setting;
	use yii\base\Widget;
	use yii\web\View;
	use Yii;

	class SettingWidget extends Widget
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
						'class' => 'yii\smarty\ViewRenderer'
					]
				]
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$settings = Setting::find()->orderBy('name')->all();

			return $this->renderFile('@settingViews/setting/index.tpl', [
				'title'    => Yii::tr('Manage settings'),
				'settings' => $settings
			]);
		}
	}