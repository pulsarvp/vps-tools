<?php

	namespace vps\tools\auth;

	use Yii;
	use yii\authclient\OAuth2;

	/**
	 * This base class oAuth provider functionality with client.
	 *
	 * @property string $clientIdDb oAuth app id from database setting name.
	 * @property string $clientSecretDb oAuth secret key from database setting name.
	 * @property string $clientUrlDb oAuth URL from database setting name.
	 *
	 * @package vps\tools\auth
	 */
	abstract class BaseClient extends OAuth2
	{

		/**
		 * Set client ID from DB settings.
		 *
		 * @see \vps\tools\components\SettingManager
		 *
		 * @param string $name
		 */
		public function setClientIdDb ($name)
		{
			$this->clientId = Yii::$app->settings->get($name);
		}

		/**
		 * Set client secret from DB settings.
		 *
		 * @see \vps\tools\components\SettingManager
		 *
		 * @param string $name
		 */
		public function setClientSecretDb ($name)
		{
			$this->clientSecret = Yii::$app->settings->get($name);
		}

		/**
		 * Set client URL from DB settings.
		 *
		 * @see \vps\tools\components\SettingManager
		 *
		 * @param string $name
		 */
		public function setClientUrlDb ($name)
		{
			$this->url = Yii::$app->settings->get($name);
		}

	}