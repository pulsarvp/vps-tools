<?php
	namespace vps\tools\modules\user\controllers;

	use vps\tools\controllers\WebController;
	use Yii;

	class RbacController extends WebController
	{

		/*
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionUserRole ()
		{
			$userClass = $this->module->modelUser;
			$post = Yii::$app->request->post();
			$user = $userClass::findOne($post[ 'id' ]);
			if (!is_null($user))
			{
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
			$userClass = $this->module->modelUser;
			$post = Yii::$app->request->post();
			$user = $userClass::findOne($post[ 'id' ]);
			if (!is_null($user))
			{
				$user->active = $post[ 'state' ];
				$user->save();
			}
			echo $post[ 'state' ];

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

