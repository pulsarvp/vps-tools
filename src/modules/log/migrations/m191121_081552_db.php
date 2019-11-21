<?php

	use vps\tools\db\Migration;

	class m191121_081552_db extends Migration
	{
		public function safeUp ()
		{
			$settingModel = 'setting';
			if (Yii::$app->has('settings') and Yii::$app->settings->hasMethod('getTableName'))
				$settingModel = Yii::$app->settings->getTableName();

			$this->insert($settingModel, [
				'name'        => 'log_db_use',
				'value'       => '0',
				'description' => 'Отправлять логи в другую БД.',
				'type'        => 'boolean',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_db_name',
				'value'       => '0',
				'description' => 'Хост для подключения к базе',
				'type'        => 'string',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_db_user',
				'value'       => '0',
				'description' => 'Пользователь для подключения к базе',
				'type'        => 'string',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_db_password',
				'value'       => '',
				'description' => 'Пароль для подключения к базе',
				'type'        => 'string',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_db_host',
				'value'       => '',
				'description' => 'Хост для подключения к базе',
				'type'        => 'string',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_db_port',
				'value'       => '',
				'description' => 'Порт для подключения к базе',
				'type'        => 'string',
				'group'       => 'log'
			]);
		}

		public function safeDown ()
		{
			$settingModel = 'setting';
			if (Yii::$app->has('settings') and Yii::$app->settings->hasMethod('getTableName'))
				$settingModel = Yii::$app->settings->getTableName();
			$this->delete($settingModel, [ 'name' => [ 'log_db_use', 'log_db_port', 'log_db_host', 'log_db_password', 'log_db_user', 'log_db_name' ] ]);
		}
	}