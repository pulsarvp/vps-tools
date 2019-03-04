<?php

	namespace vps\tools\modules\user\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\helpers\Html;
	use vps\tools\helpers\Url;
	use vps\tools\modules\log\components\LogManager;
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

				LogManager::info(Yii::tr('Пользователю {user} забрали права {roles}', [ 'user' => Html::a($user->name, Url::toRoute([ 'user/view', 'id' => $user->id ])), 'roles' => implode(',', $user->getRolesNames()) ]));
				$user->revokeAllRoles();
				if (is_array($post[ 'roles' ]))
				{
					foreach ($post[ 'roles' ] as $item)
					{
						$user->assignRole($item);
					}
					LogManager::info(Yii::tr('Пользователю {user} добавили права {roles}', [ 'user' => Html::a($user->name, Url::toRoute([ 'user/view', 'id' => $user->id ])), 'roles' => implode(',', $post[ 'roles' ]) ]));
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

