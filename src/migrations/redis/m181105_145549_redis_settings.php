<?php

	use vps\tools\db\Migration;

	class m181105_145549_redis_settings extends Migration
	{
		public function safeUp ()
		{
			$this->batchInsert('setting', [ 'name', 'value', 'group', 'type', 'description' ], [
				[
					'redis_hostname',
					'localhost',
					'redis',
					'string',
					'Redis: хост.'
				],
				[
					'redis_database',
					'0',
					'redis',
					'integer',
					'Redis: номер базы.'
				],
				[
					'redis_port',
					'6379',
					'redis',
					'string',
					'Redis: порт.'
				],
				[
					'redis_password',
					'',
					'redis',
					'string',
					'Redis: пароль.'
				],
			]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'redis_hostname', 'redis_database', 'redis_port', 'redis_password' ] ]);
		}
	}