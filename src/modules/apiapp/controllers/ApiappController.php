<?php

	namespace vps\tools\modules\apiapp\controllers;

	use app\base\Controller;
	use Yii;

	/**
	 * Class AppController
	 *
	 * @package vps\tools\modules\apiapp\controllers
	 */
	class ApiappController extends Controller
	{

		/**
		 * @inheritdoc
		 */
		public function beforeAction ($action)
		{
			if (parent::beforeAction($action) and Yii::$app->request->isAjax)
			{
				if (!Yii::$app->user->identity->active or !( Yii::$app->user->can('admin') or Yii::$app->user->can('admin_apiapp') ))
				{
					return false;
				}

				return true;
			}
			else
				return false;
		}

	}

