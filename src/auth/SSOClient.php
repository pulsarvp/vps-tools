<?php

	namespace vps\tools\auth;

	use Yii;

	/**
	 * This class performs oAuth provider functionality with SSO client.
	 *
	 * @property string $clientIdDb     oAuth app id from database setting name.
	 * @property string $clientSecretDb oAuth secret key from database setting name.
	 * @property string $clientUrlDb    oAuth URL from database setting name.
	 * @property string $url            Base URL to perform request.
	 *
	 * @package vps\tools\auth
	 */
	class SSOClient extends BaseClient
	{
		/**
		 * Set all necessary URLs by using given one as base.
		 *
		 * @param string $url
		 */
		public function setUrl ($url)
		{
			$this->authUrl = $url . '/oauth2/authorize';
			$this->tokenUrl = $url . '/oauth2/access_token';
			$this->apiBaseUrl = $url . '/users';
			$this->returnUrl = Yii::$app->request->hostInfo . '/' . Yii::$app->request->pathInfo;
		}

		/**
		 * @inheritdoc
		 * Gets email, name and profile.
		 */
		public function defaultNormalizeUserAttributeMap ()
		{
			return [
				'email'        => 'email',
				'image' => function ($attributes)
				{
					return isset($attributes[ 'icon_profile' ]) ? $attributes[ 'icon_profile' ] : '';
				},
				'name'         => function ($attributes)
				{
					return trim(implode(' ', [ $attributes[ 'lastname' ], $attributes[ 'firstname' ], $attributes[ 'secondname' ] ]));
				},
				'profile'      => function ($attributes)
				{
                    $name = isset($attributes[ 'user_id' ]) ? $attributes[ 'user_id' ] : $attributes[ 'username' ];

                    return $name . '@' . $this->name;
				},
				'roles'        => function ($attributes)
				{
					return isset($attributes[ 'roles' ]) ? $attributes[ 'roles' ] : null;
				}
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultName ()
		{
			return 'npoed';
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultTitle ()
		{
			return 'npoed';
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultViewOptopns ()
		{
			return [
				'popupHeight' => 400,
				'popupWidth'  => 600,
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function initUserAttributes ()
		{
			$token = $this->getAccessToken();
			if (!is_object($token))
			{
				Yii::$app->getSession()->removeAll();
				$token = $this->restoreAccessToken();
			}
			if (is_object($token))
				return $this->api('me', 'GET', [], [ 'Authorization' => 'Bearer ' . $token->token ]);
			else
				return false;
		}

	}
