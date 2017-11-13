<?php

	namespace vps\tools\modules\apiapp\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\modules\user\models\User;
	use Yii;

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
				$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
				if (!Yii::$app->user->identity->active or !( array_key_exists(User::R_ADMIN, $roles) or array_key_exists('admin_apiapp', $roles) ))
				{
					return false;
				}

				return true;
			}
			else
				return false;
		}

	}

