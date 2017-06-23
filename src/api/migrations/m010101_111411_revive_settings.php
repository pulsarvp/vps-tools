<?php

	use yii\db\Migration;

	/**
	 * Class m010101_111411_revive_settings
	 */
	class m010101_111411_revive_settings extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{

			$banner_api_url = 'http://revive/api/v2/xmlrpc/';
			$banner_api_login = '';
			$banner_api_password = '';
			if (Yii::$app->controller->interactive)
			{
				$banner_api_url = Yii::$app->controller->prompt('Enter link for the Revive API', [ 'default' => $banner_api_url ]);
				$banner_api_login = Yii::$app->controller->prompt('Enter login for the Revive API', [ 'default' => $banner_api_login ]);
				$banner_api_password = Yii::$app->controller->prompt('Enter password for the Revive API', [ 'default' => $banner_api_password ]);
			}

			$this->insert('setting', [
				'name'        => 'banner_api_url',
				'value'       => $banner_api_url,
				'description' => 'Базовая ссылка для доступа к Revive API.'
			]);
			$this->insert('setting', [
				'name'        => 'banner_api_login',
				'value'       => $banner_api_login,
				'description' => 'Логин для доступа к Revive API.'
			]);
			$this->insert('setting', [
				'name'        => 'banner_api_password',
				'value'       => $banner_api_password,
				'description' => 'Пароль для доступа к Revive API.'
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [
				'name' => [ 'banner_api_url', 'banner_api_login', 'banner_api_password' ]
			]);
		}
	}