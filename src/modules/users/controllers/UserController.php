<?php
	namespace vps\tools\modules\users\controllers;

	use vps\tools\controllers\WebController;
	use vps\tools\helpers\TimeHelper;
	use Yii;

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

		public function actionLogin ()
		{
		}

		public function actionLogout ()
		{
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
