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
							'denyCallback' => function ($rule, $action) {
								Yii::$app->notification->errorToSession(Yii::tr('You are already logged in.', [], 'user'));
								$this->redirect(Url::toRoute([ '/user/index' ]));
							}
						],
						[ 'allow' => true, 'actions' => [ 'index', 'logout' ], 'roles' => [ '@' ] ],
						[
							'allow'         => true,
							'actions'       => [ 'manage', 'delete' ],
							'roles'         => [ '@' ],
							'matchCallback' => function ($rule, $action) {
								if (!Yii::$app->user->identity->active)
								{
									Yii::$app->notification->errorToSession(Yii::tr('Your account is not approved yet.', [], 'user'));
									$action->controller->redirect(Url::toRoute([ '/site/index' ]));
								}
								elseif (!( Yii::$app->user->can('admin') or Yii::$app->user->can('admin_user') ))
								{
									Yii::$app->notification->errorToSession(Yii::tr('You have no permissions.', [], 'user'));
									$action->controller->redirect(Url::toRoute([ '/site/index' ]));
								}

								return true;
							}
						],

					],
				],
			];
		}

		public function actionDelete ($id)
		{
			if (Yii::$app->user->can(User::R_ADMIN))
			{
				$userClass = $this->module->modelUser;
				if ($id != Yii::$app->user->id)
				{
					$user = $userClass::findOne($id);
					$user->delete();
				}
			}

			$this->redirect(Yii::$app->request->referrer);
		}

		public function actionIndex ()
		{
			$this->title = Yii::tr('User', [], 'user');
			$this->_tpl = '@userViews/index';
			$userClass = $this->module->modelUser;
			$user = $userClass::findOne(Yii::$app->user->id);
			$this->data('user', $user);
		}

		public function actionManage ()
		{
			$this->title = Yii::tr('User manage', [], 'user');
			$this->_tpl = '@userViews/manage';
		}

		public function actionCancel ()
		{
			$collection = Yii::$app->authClientCollection;
			$client = $collection->getClient(Yii::$app->session->get('client'));
			Yii::$app->notification->errorToSession(Yii::tr('You have decline the authorization via {client}.', [ 'client' => $client->title ], 'user'));
			$this->redirect(Url::toRoute([ '/user/login' ]));
		}

		public function actionLogin ()
		{
			$this->title = Yii::tr('Login', [], 'user');
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
			if (method_exists($client, 'successAuth'))
			{
				$user = $client->successAuth();
			}
			else
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

						if (is_array($attributes[ 'roles' ]) and in_array(User::R_ADMIN, $attributes[ 'roles' ]))
							$user->active = 1;
					}
					if ($attributes[ 'image' ] and $user->image != $attributes[ 'image' ])
					{
						$user->image = $attributes[ 'image' ];
					}
					$user->save();
				}
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
	}
