<?php

	use vps\tools\db\Migration;

	class m181210_061223_kafka_source extends Migration
	{
		public function safeUp ()
		{
			$this->update('setting', [ 'description' => 'Название топиков для получение из Kafka.' ], [ 'name' => 'kafka_topic' ]);
			$this->insert('setting', [ 'name' => 'kafka_source', 'value' => '', 'description' => 'Название топика для отправки в Kafka.', 'group' => 'kafka', 'type' => 'string' ]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'kafka_source' ] ]);
		}
	}