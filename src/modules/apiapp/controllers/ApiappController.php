<?php
	namespace vps\tools\modules\apiapp\controllers;

	use vps\tools\controllers\WebController;
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
				return true;
			}
			else
				return false;
		}
		
	}

