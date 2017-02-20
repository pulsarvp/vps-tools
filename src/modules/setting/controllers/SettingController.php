<?php
	namespace vps\tools\modules\setting\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\setting\models\Setting;
	use Yii;

	class SettingController extends WebController
	{
		public function actionIndex ()
		{
			$settings = Setting::find()->orderBy('name')->all();
			$this->data('settings', $settings);

			$this->title = Yii::tr('Manage settings');
			$this->_tpl = '../../vendor/miptliot/vps-tools/src/modules/setting/views/setting/index';
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionEdit ()
		{
			if (Yii::$app->request->isAjax)
			{
				$setting = Setting::findOne([ 'name' => Yii::$app->request->post('name') ]);
				if ($setting !== null)
				{
					$setting->setAttributes([ 'value' => Yii::$app->request->post('value'), 'description' => Yii::$app->request->post('description') ]);
					if (!$setting->save())
						echo 0;

					echo 1;
				}
			}
			Yii::$app->end();
		}
	}

	?>