<?php
	namespace vps\tools\modules\setting\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\setting\models\Setting;
	use Yii;
	use yii\helpers\Json;

	class SettingController extends WebController
	{
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
						echo Json::encode(current($setting->firstErrors));
					else
						echo 0;
				}
			}
			Yii::$app->end();
		}
	}

