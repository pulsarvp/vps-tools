<?php

	namespace vps\tools\modules\setting\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\setting\models\Setting;
	use vps\tools\modules\user\models\User;
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
				$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
				if (!Yii::$app->user->identity->active or !( array_key_exists(User::R_ADMIN, $roles) or array_key_exists('admin_setting', $roles) ))
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

