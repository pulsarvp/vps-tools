<?php
	namespace vps\tools\auth;

	use Yii;
	use yii\authclient\OAuth2;

	/**
	 * @property-read string  $logoutUrl
	 * @property-write string clientIdDb
	 * @property-write string clientSecretDb
	 * @property-write string clientUrlDb
	 */
	class PulsarSsoClient extends OAuth2
	{

		/**
		 * @var string Url for accessing oAuth2 API.
		 */
		private $_url;

		public function setUrl ($url)
		{
			$this->_url = $url;
			$this->authUrl = $url . '/oauth2/authorize';
			$this->tokenUrl = $url . '/oauth/token';
			$this->apiBaseUrl = $url . '/oauth';
		}

		public function setClientIdDb ($name)
		{
			$this->clientId = Yii::$app->settings->get($name);
		}

		public function setClientSecretDb ($name)
		{
			$this->clientSecret = Yii::$app->settings->get($name);
		}

		public function setClientUrlDb ($name)
		{
			$this->url = Yii::$app->settings->get($name);
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
				'profile' => function ($attributes)
				{
					return $attributes[ 'id' ] . '@' . $this->name;
				}
			];
		}

		protected function defaultName ()
		{
			return 'psso';
		}

		protected function defaultTitle ()
		{
			return 'PSSO';
		}

		protected function defaultViewOptopns ()
		{
			return [
				'popupHeight' => 400,
				'popupWidth'  => 600,
			];
		}

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