<?php
	namespace vps\tools\web;

	class User extends \yii\web\User
	{
		/**
		 * @inheritdoc.
		 */
		public $identityClass = 'vps\tools\modules\users\models\User';
		/**
		 * @inheritdoc.
		 */
		public $authTimeou = 86400;
		/**
		 * @inheritdoc.
		 */
		public $enableAutoLogin = true;
		/**
		 * @inheritdoc.
		 */
		public $loginUrl = [ 'user/login' ];

	}