<?php
	namespace vps\tools\modules\app\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\helpers\StringHelper;
	use vps\tools\modules\app\models\App;
	use Yii;
	use yii\filters\AccessControl;
	use yii\helpers\Json;

	/**
	 * Class AppController
	 *
	 * @package vps\tools\modules\app\controllers
	 */
	class AppController extends WebController
	{

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionEdit ()
		{
			if (Yii::$app->request->isAjax)
			{
				$apiapp = App::findOne(Yii::$app->request->post('id'));
				if ($apiapp !== null)
				{
					$apiapp->setAttributes([ 'name' => Yii::$app->request->post('name'), 'token' => Yii::$app->request->post('token') ]);
					if (!$apiapp->save())
						echo Json::encode(current($apiapp->firstErrors));
					else
						echo 0;
				}
			}
			Yii::$app->end();
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionDelete ()
		{
			if (Yii::$app->request->isAjax)
			{
				$apiapp = App::findOne(Yii::$app->request->post('id'));
				if ($apiapp !== null)
				{
					if (!$apiapp->delete())
						echo Json::encode(current($apiapp->firstErrors));
					else
						echo 0;
				}
			}
			Yii::$app->end();
		}
	}

