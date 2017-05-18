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
				'email'   => 'email',
				'name'    => function ($attributes)
				{
					return trim(implode(' ', [ $attributes[ 'lastname' ], $attributes[ 'firstname' ], $attributes[ 'secondname' ] ]));
				},
				'profile' => function ($attributes)
				{
					return $attributes[ 'user_id' ] . '@' . $this->name;
				},
				'roles'   => function ($attributes)
				{
					return '';
				}
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultName ()
		{
			return 'sso';
		}

		/**
		 * @inheritdoc
		 */
		protected function defaultTitle ()
		{
			return 'sso';
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
			return $this->api('me', 'GET');
		}

	}

	?>