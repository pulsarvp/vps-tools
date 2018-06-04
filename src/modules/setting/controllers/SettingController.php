<?php

	namespace vps\tools\modules\setting\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\setting\models\Setting;
	use Yii;
	use yii\helpers\Json;

	class SettingController extends WebController
	{
		/**
		 * @inheritdoc
		 */
		public function beforeAction ($action)
		{
			if (parent::beforeAction($action) and Yii::$app->request->isAjax)
			{
				if (!Yii::$app->user->identity->active or !$this->canEdit())
				{
					return false;
				}

				return true;
			}
			else
				return false;
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionEdit ()
		{
			if (Yii::$app->request->isAjax)
			{
				$setting = Setting::findOne([ 'name' => Yii::$app->request->post('name') ]);
				if ($setting !== null and $setting->fixed == 0)
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

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionValue ()
		{
			if (Yii::$app->request->isAjax)
			{
				$setting = Setting::findOne([ 'name' => Yii::$app->request->post('name') ]);
				if ($setting !== null)
				{
					echo Json::encode($setting->value);
				}
			}
			Yii::$app->end();
		}

		private function canEdit ()
		{
			return Yii::$app->user->can('admin') or Yii::$app->user->can('setting_edit');
		}
	}

