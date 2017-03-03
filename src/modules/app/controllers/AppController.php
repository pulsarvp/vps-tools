<?php
	namespace vps\tools\modules\app\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\app\models\App;
	use Yii;
	use yii\filters\AccessControl;
	use yii\helpers\Json;

	/**
	 * Class AppController
	 * @package vps\tools\modules\app\controllers
	 */
	class AppController extends WebController
	{

		/** @inheritdoc */
		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[ 'allow' => true, 'actions' => [ 'index', 'edit', 'delete' ], 'roles' => [ '@' ] ],
					],
				],
			];
		}

		/**
		 * List application
		 */
		public function actionIndex ()
		{
			if (!empty( $this->module->permission ))
				$apiapps = App::find()->orderBy('name')->all();
			else
				$apiapps = [];
			$this->data('apiapps', $apiapps);
			$this->addApp();
			$this->title = Yii::tr('Manage api app');
			$this->data('module', $this->module);
			$this->_tpl = '@appViews/app/index';
		}

		/**
		 * Add new app and token
		 */
		private function addApp ()
		{
			$appnew = new App();
			if (in_array('create', $this->module->permission) and Yii::$app->request->post('method') == 'apiapp-add')
			{
				$appnew->setAttributes(Yii::$app->request->post('App'));
				if ($appnew->validate())
				{
					$appnew->save();
					$this->redirect('index#' . $appnew->name);
					$appnew->setAttributes([ 'name' => '', 'token' => '' ]);
				}
			}
			$this->data('appnew', $appnew);
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionEdit ()
		{
			if (in_array('edit', $this->module->permission) and Yii::$app->request->isAjax)
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
			if (in_array('delete', $this->module->permission) and Yii::$app->request->isAjax)
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

