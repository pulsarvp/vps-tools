<?php

	use vps\tools\db\Migration;

	class m191115_155749_forceClusterMode extends Migration
	{
		public function safeUp ()
		{
			$this->batchInsert('setting', [ 'name', 'value', 'group', 'type', 'description' ], [
				[
					'cache_redis_force_cluster',
					0,
					'cache',
					'boolean',
					'Redis: в кластере или нет.'
				]
			]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'cache_redis_force_cluster' ] ]);
		}
	}