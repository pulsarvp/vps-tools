<?php
	namespace vps\tools\modules\apiapp\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\apiapp\models\Apiapp;
	use Yii;
	use yii\helpers\Json;

	/**
	 * Class AppController
	 *
	 * @package vps\tools\modules\apiapp\controllers
	 */
	class ApiappController extends WebController
	{

		/**
		 * @inheritdoc
		 */
		public function beforeAction ($action)
		{
			if (parent::beforeAction($action) and Yii::$app->request->isAjax)
			{
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
			$apiapp = Apiapp::findOne(Yii::$app->request->post('id'));
			if ($apiapp !== null)
			{
				$apiapp->setAttributes([ 'name' => Yii::$app->request->post('name'), 'token' => Yii::$app->request->post('token') ]);
				if (!$apiapp->save())
					echo Json::encode($apiapp->errors);
				else
					echo 0;
			}
			Yii::$app->end();
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionDelete ()
		{
			$apiapp = Apiapp::findOne(Yii::$app->request->post('id'));
			if ($apiapp !== null)
			{
				if (!$apiapp->delete())
					echo Json::encode(current($apiapp->firstErrors));
				else
					echo 0;
			}

			Yii::$app->end();
		}
	}

