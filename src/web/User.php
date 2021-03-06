<?php

	namespace vps\tools\web;

	use Yii;

	class User extends \yii\web\User
	{
		/**
		 * @inheritdoc.
		 */
		public $identityClass = 'vps\tools\modules\user\models\User';
		/**
		 * @inheritdoc.
		 */
		public $authTimeout = 86400;
		/**
		 * @inheritdoc.
		 */
		public $enableAutoLogin = true;
		/**
		 * @inheritdoc.
		 */
		public $loginUrl = [ 'user/login' ];

		/**
		 * Set authTimeout.
		 *
		 * @param string $name
		 */
		public function setAuthTimeoutDb ($name = 'user_auth_timeout')
		{
			$this->authTimeout = Yii::$app->settings->get($name);
		}

	}