<?php

	namespace vps\tools\modules\user\controllers;

	use vps\tools\auth\AuthAction;
	use vps\tools\controllers\WebController;
	use vps\tools\helpers\StringHelper;
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
						[
							'allow'        => false,
							'actions'      => [ 'auth', 'login', 'cancel' ],
							'roles'        => [ '@' ],
							'denyCallback' => function ($rule, $action)
							{
								Yii::$app->notification->errorToSession(Yii::tr('You are already logged in.', [], 'user'));
								$this->redirect(Url::toRoute([ '/user/index' ]));
							}
						],
						[ 'allow' => true, 'actions' => [ 'index', 'logout' ], 'roles' => [ '@' ] ],
						[
							'allow'         => true,
							'actions'       => [ 'manage' ],
							'roles'         => [ '@' ],
							'matchCallback' => function ($rule, $action)
							{
								if (!Yii::$app->user->identity->active)
									$this->redirect(Url::toRoute([ '/user/index' ]));
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
			$this->redirect(Url::toRoute([ '/user/login' ]));
		}

		public function actionLogin ()
		{
			$this->_tpl = '@userViews/login';
			$defaultClient = Yii::$app->settings->get('auth_client_default', $this->module->defaultClient);
			$this->data('defaultClient', $defaultClient);
		}

		public function actionLogout ()
		{
			$this->_tpl = '@userViews/logout';
			$referrer = Yii::$app->getRequest()->getReferrer();
			Yii::$app->user->logout();
			if ($this->module->redirectAfterLogout)
			{
				foreach ($this->module->guestRestrictedRoutes as $url)
				{
					if (StringHelper::pos($referrer, Url::toRoute([ $url ])))
					{
						$this->redirect(Yii::$app->user->returnUrl);
						Yii::$app->end();
					}
				}
			}
			else
			{
				$this->redirect(Yii::$app->user->returnUrl);
				Yii::$app->end();
			}

			$this->redirect($referrer);
			Yii::$app->end();
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
					->one();

				if ($user == null)
				{
					$user = $userClass::find()
						->where([ 'email' => $attributes[ 'email' ] ])
						->one();
				}

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
					if ($this->module->redirectAfterLogin)
						$this->redirect(Yii::$app->getUser()->getReturnUrl());
					else
						$this->redirect(Url::toRoute([ '/site/index' ]));
					Yii::$app->end();
				}
			}
			else
				throw new \Exception(Yii::tr('Wrong oAuth2 server response.', [], 'user'));
		}
	}
