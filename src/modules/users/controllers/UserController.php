<?php
	namespace vps\tools\modules\users\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\helpers\Url;
	use Yii;
	use yii\filters\AccessControl;

	class UserController extends WebController
	{
		public function actions ()
		{
			return [
				'auth' => [
					'class'           => 'yii\authclient\AuthAction',
					'successCallback' => [ $this, 'successAuth' ]
				]
			];
		}

		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow'       => true,
							'actions'     => [],
							'controllers' => [],
							'roles'       => [ '@' ]
						],
						[
							'allow'        => true,
							'actions'      => [ 'auth', 'login' ],
							'controllers'  => [ 'user' ],
							'roles'        => [ '?' ],
							'denyCallback' => function ($rule, $action) { $this->error('You are already logged in.'); },
						],
						[
							'allow'         => true,
							'actions'       => [ 'management' ],
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
			$userClass = Yii::$app->getModule('users')->modelUser;
			$user = $userClass::findOne(Yii::$app->user->id);
			$this->data('user', $user);
		}

		public function actionManagement ()
		{
			$this->_tpl = '@userViews/management';
		}

		public function actionLogin ()
		{
			$this->_tpl = '@userViews/login';
			$this->data('defaultClient', $userClass = Yii::$app->getModule('users')->defaultClient);
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
			$userClass = Yii::$app->getModule('users')->modelUser;
			$attributes = $client->getUserAttributes();
			if (is_array($attributes) and isset( $attributes[ 'email' ] ))
			{
				$user = $userClass::find()->where([ 'profile' => $attributes[ 'profile' ] ])->one();

				if ($user == null)
					$user = $userClass::find()->where([ 'email' => $attributes[ 'email' ] ])->one();

				if ($user == null)
				{
					$user = new $userClass;
					$user->register($attributes[ 'name' ], $attributes[ 'email' ], $attributes[ 'profile' ]);
				}

				if ($user == null or !isset( $user->id ))
					throw new \Exception('Authorization failed.');
				elseif ($user->active == false)
				{
					Yii::$app->notification->errorToSession(Yii::tr('Your account is not approved yet.', [], 'user'));
				}
				else
				{
					$user->loginDT = TimeHelper::now();
					$user->save();

					Yii::$app->user->login($user, 604800);
				}
			}
			else
				throw new \Exception(Yii::tr('Wrong oAuth2 server response.', [], 'user'));
		}
	}
