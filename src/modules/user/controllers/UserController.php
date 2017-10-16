<?php

	namespace vps\tools\modules\user\controllers;

	use vps\tools\auth\AuthAction;
	use vps\tools\controllers\WebController;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\user\models\User;
	use Yii;
	use yii\filters\AccessControl;

	class UserController extends WebController
	{
		public function actions ()
		{
			return [
				'auth' => [
					'class'           => AuthAction::className(),
					'successCallback' => [ $this, 'successAuth' ],
					'cancelUrl'       => Url::toRoute([ 'user/cancel' ])
				]
			];
		}

		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[ 'allow' => true, 'actions' => [ 'auth', 'login', 'cancel' ], 'roles' => [ '?' ] ],
						[ 'allow' => true, 'actions' => [ 'index', 'logout' ], 'roles' => [ '@' ] ],
						[
							'allow'         => true,
							'actions'       => [ 'manage' ],
							'roles'         => [ '@' ],
							'matchCallback' => function ($rule, $action)
							{
								if (!Yii::$app->user->identity->active)
									$this->redirect(Url::toRoute('user/index'));
								else
									return true;
							}
						],

					],
				],
			];
		}

		public function actionIndex ()
		{
			$this->title = 'User';
			$this->_tpl = '@userViews/index';
			$userClass = $this->module->modelUser;
			$user = $userClass::findOne(Yii::$app->user->id);
			$this->data('user', $user);
		}

		public function actionManage ()
		{
			$this->_tpl = '@userViews/manage';
		}

		public function actionCancel ()
		{
			Yii::$app->notification->errorToSession(Yii::tr('You have rejected the authorization request.', [], 'user'));
			$this->redirect(Url::toRoute([ 'user/login' ]));
		}

		public function actionLogin ()
		{
			$this->_tpl = '@userViews/login';
			$this->data('defaultClient', $userClass = $userClass = $this->module->defaultClient);
		}

		public function actionLogout ()
		{
			$this->_tpl = '@userViews/logout';
			Yii::$app->user->logout();
			$this->redirect('/');
		}

		/**
		 * @param \yii\authclient\BaseClient $client
		 *
		 * @throws \Exception
		 */
		public function successAuth ($client)
		{
			$userClass = $this->module->modelUser;
			$attributes = $client->getUserAttributes();
			if (is_array($attributes) and isset($attributes[ 'email' ]))
			{
				/** @var User $user */
				$user = $userClass::find()
					->where([ 'profile' => $attributes[ 'profile' ] ])
					->orWhere([ 'email' => $attributes[ 'email' ] ])
					->one();

				if ($user == null)
				{
					$user = new $userClass;
					$user->register($attributes[ 'name' ], $attributes[ 'email' ], $attributes[ 'profile' ], $this->module->autoactivate);

					if ($attributes[ 'roles' ])
						$user->assignRoles($attributes[ 'roles' ]);
					else
						$user->assignRole($this->module->defaultRole);
				}

				if ($attributes[ 'image' ] and $user->image != $attributes[ 'image' ])
				{
					$user->image = $attributes[ 'image' ];
					$user->save();
				}

				if ($user == null or !isset($user->id))
					throw new \Exception(Yii::tr('Authorization failed.', [], 'user'));
				elseif ($user->active == false)
				{
					Yii::$app->notification->errorToSession(Yii::tr('Your account is not approved yet.', [], 'user'));
				}
				else
				{
					$user->loginDT = TimeHelper::now();
					$user->save();

					Yii::$app->user->login($user, Yii::$app->user->authTimeout);
				}
			}
			else
				throw new \Exception(Yii::tr('Wrong oAuth2 server response.', [], 'user'));
		}
	}
