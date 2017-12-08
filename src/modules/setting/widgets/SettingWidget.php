<?php

	namespace vps\tools\modules\setting\widgets;

	use vps\tools\modules\setting\models\Setting;
	use Yii;
	use yii\base\Widget;
	use yii\web\View;

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
						'class'   => 'yii\smarty\ViewRenderer',
						'imports' => [
							'Html' => '\vps\tools\helpers\Html',
							'Url'  => '\vps\tools\helpers\Url'
						]
					]
				]
			]);
			Yii::$app->view->registerCss('#setting-list .value {font-family: Menlo, monospace; overflow-wrap: break-word; word-wrap: break-word; max-width: 500px} .setting-group{cursor:pointer;border-bottom:1px dashed}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$message = null;
			$groups = Setting::find()->select('group')->orderBy([ 'group' => SORT_ASC ])->groupBy('group')->column();
			if (array_search(Setting::G_GENERAL, $groups) !== false)
				$settings[ Setting::G_GENERAL ] = Setting::find()->where([ 'group' => Setting::G_GENERAL ])->orderBy([ 'name' => SORT_ASC ])->all();
			foreach ($groups as $group)
			{
				$settings[ $group ] = Setting::find()->where([ 'group' => $group ])->orderBy([ 'name' => SORT_ASC ])->all();
			}
			if (!$this->canEdit())
				$message = [ 'class' => 'warning', 'message' => Yii::tr('You can view but not edit the settings.', [], 'setting') ];
			if (!$this->canView())
				$message = [ 'class' => 'danger', 'message' => Yii::tr('You cannot view the settings.', [], 'setting') ];

			return $this->renderFile('@settingViews/index.tpl', [
				'title'    => Yii::tr('Manage settings'),
				'settings' => $settings,
				'canEdit'  => $this->canEdit(),
				'canView'  => $this->canView(),
				'message'  => $message
			]);
		}

		private function canView ()
		{
			return Yii::$app->user->can('admin') or Yii::$app->user->can('setting_view') or Yii::$app->user->can('setting_edit');
		}

		private function canEdit ()
		{
			return Yii::$app->user->can('admin') or Yii::$app->user->can('setting_edit');
		}
	}