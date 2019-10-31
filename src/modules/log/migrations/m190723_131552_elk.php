<?php

	use vps\tools\db\Migration;

	class m190723_131552_elk extends Migration
	{
		public function safeUp ()
		{
			$settingModel = 'setting';
			if (Yii::$app->has('settings') and Yii::$app->settings->hasMethod('getTableName'))
				$settingModel = Yii::$app->settings->getTableName();

			$this->insert($settingModel, [
				'name'        => 'log_elk_use',
				'value'       => '0',
				'description' => 'Отправлять логи в ELK.',
				'type'        => 'boolean',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_ssl_use',
				'value'       => '0',
				'description' => 'Использовать сертефикат SSL для ELK.',
				'type'        => 'boolean',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_elk_dns',
				'value'       => '',
				'description' => 'DNS ELK.',
				'type'        => 'string',
				'group'       => 'log'
			]);
			$this->insert($settingModel, [
				'name'        => 'log_elk_cert',
				'value'       => '',
				'description' => 'Путь файла сертификата',
				'type'        => 'string',
				'group'       => 'log'
			]);
		}

		public function safeDown ()
		{
			$settingModel = 'setting';
			if (Yii::$app->has('settings') and Yii::$app->settings->hasMethod('getTableName'))
				$settingModel = Yii::$app->settings->getTableName();
			$this->delete($settingModel, [ 'name' => [ 'log_elk_use', 'log_elk_dns', 'log_elk_cert', 'log_ssl_use' ] ]);
		}
	}