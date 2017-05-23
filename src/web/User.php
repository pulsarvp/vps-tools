<?php
	namespace vps\tools\web;

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

	}