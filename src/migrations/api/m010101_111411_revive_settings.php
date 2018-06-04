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

			$banner_use = '1';
			$banner_api_url = 'http://revive/api/v2/xmlrpc/';
			$banner_api_login = '';
			$banner_api_password = '';
			if (Yii::$app->controller->interactive)
			{
				$banner_use = Yii::$app->controller->prompt('Show banners. [0/1]', [ 'default' => $banner_use ]);
				$banner_api_url = Yii::$app->controller->prompt('Enter link for the Revive API', [ 'default' => $banner_api_url ]);
				$banner_api_login = Yii::$app->controller->prompt('Enter login for the Revive API', [ 'default' => $banner_api_login ]);
				$banner_api_password = Yii::$app->controller->prompt('Enter password for the Revive API', [ 'default' => $banner_api_password ]);
			}

			$this->insert('setting', [
				'name'        => 'banner_use',
				'value'       => $banner_use,
				'description' => 'Показывать баннеры.',
				'type'        => 'boolean',
				'group'       => 'banner'
			]);
			$this->insert('setting', [
				'name'        => 'banner_api_url',
				'value'       => $banner_api_url,
				'description' => 'Базовая ссылка для доступа к Revive API.',
				'type'        => 'url',
				'group'       => 'banner'
			]);
			$this->insert('setting', [
				'name'        => 'banner_api_login',
				'value'       => $banner_api_login,
				'description' => 'Логин для доступа к Revive API.',
				'type'        => 'string',
				'group'       => 'banner'
			]);
			$this->insert('setting', [
				'name'        => 'banner_api_password',
				'value'       => $banner_api_password,
				'description' => 'Пароль для доступа к Revive API.',
				'type'        => 'string',
				'group'       => 'banner'
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [
				'name' => [ 'banner_use', 'banner_api_url', 'banner_api_login', 'banner_api_password' ]
			]);
		}
	}