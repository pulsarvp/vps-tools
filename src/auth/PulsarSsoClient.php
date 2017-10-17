<?php

	namespace vps\tools\auth;

	use Yii;

	/**
	 * This class performs oAuth provider functionality with Pulsar SSO client.
	 *
	 * @property string $clientIdDb     oAuth app id from database setting name.
	 * @property string $clientSecretDb oAuth secret key from database setting name.
	 * @property string $clientUrlDb    oAuth URL from database setting name.
	 * @property string $url            Base URL to perform request.
	 *
	 * @package vps\tools\auth
	 */
	class PulsarSsoClient extends BaseClient
	{
		/**
		 * @var string Logout url.
		 */
		private $_logoutUrl;

		/**
		 * @var string Url for accessing oAuth2 API.
		 */
		private $_url;

		/**
		 * Creates logout URL.
		 *
		 * @param string $redirectTo
		 *
		 * @return string
		 */
		public function getLogoutUrl ($redirectTo = null)
		{
			if (empty($redirectTo))
				return $this->_logoutUrl;
			else
				return $this->_logoutUrl . '?next=' . urlencode($redirectTo);
		}

		/**
		 * @return string return URL.
		 */
		public function getReturnUrl ()
		{
			$returnUrl = parent::getReturnUrl();

			if ($returnUrl === $this->defaultReturnUrl())
				return Yii::$app->request->hostInfo . '/' . Yii::$app->request->pathInfo;
			else
				return Yii::$app->request->hostInfo . '/' . $returnUrl;
		}

		/**
		 * Set all necessary URLs by using given one as base.
		 *
		 * @param string $url
		 */
		public function setUrl ($url)
		{
			$this->_url = $url;
			$this->_logoutUrl = $url . '/user/logout/';
			$this->authUrl = $url . '/oauth2/authorize';
			$this->tokenUrl = $url . '/oauth/token';
			$this->apiBaseUrl = $url . '/oauth';
		}

		/**
		 * @inheritdoc
		 * Gets email, name and profile.
		 */
		public function defaultNormalizeUserAttributeMap ()
		{
			return [
				'email'   => 'email',
				'name'    => 'username',
				'image'   => function ($attributes)
				{
					return isset($attributes[ 'image' ]) ? $attributes[ 'image' ] : '';
				},
				'profile' => function ($attributes)
				{
					return $attributes[ 'id' ] . '@' . $this->name;
				},
				'roles'   => function ($attributes)
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
			return 'pulsar';
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultTitle ()
		{
			return 'Pulsar';
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