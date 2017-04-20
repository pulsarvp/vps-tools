<?php
	namespace vps\tools\modules\rbac\controllers;

	use common\models\User;
	use vps\tools\controllers\WebController;
	use Yii;

	class RbacController extends WebController
	{

		/*
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionUserRole ()
		{
			if (Yii::$app->request->isAjax)
			{

				$post = Yii::$app->request->post();
				$user = User::findOne($post[ 'id' ]);
				$user->revokeAllRoles();
				foreach ($post[ 'roles' ] as $item)
				{
					$user->assignRole($item);
				}
			}
			Yii::$app->end();
		}

		/*
		 * This action is for AJAX request.  It is updated user state
		 */
		public function actionUserState ()
		{
			if (Yii::$app->request->isAjax)
			{

				$user = User::findOne(Yii::$app->request->post('id'));
				if ($user)
				{
					$user->active = Yii::$app->request->post('state');
					$user->save();
				}
				echo Yii::$app->request->post('state');
			}
			Yii::$app->end();
		}

		/*
		 * This action is for AJAX request.  It is updated user state
		 */
		public function actionDeleteRole ()
		{

			$auth = Yii::$app->getAuthManager();
			$role = $auth->getRole(Yii::$app->request->get('id'));
			$auth->remove($role);
			$url = Yii::$app->request->referrer . '#roles';
			Yii::$app->response->redirect($url);

			Yii::$app->end();
		}
	}

