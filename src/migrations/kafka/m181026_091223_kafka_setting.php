<?php

	use vps\tools\db\Migration;

	class m181026_091223_kafka_setting extends Migration
	{
		public function safeUp ()
		{
			$this->insert('setting', [ 'name' => 'kafka_use', 'value' => 0, 'description' => 'Отправлять данные в Kafka.', 'group' => 'kafka', 'type' => 'boolean' ]);
			$this->insert('setting', [ 'name' => 'kafka_host', 'value' => '', 'description' => 'Host для Kafka.', 'group' => 'kafka', 'type' => 'string' ]);
			$this->insert('setting', [ 'name' => 'kafka_port', 'value' => '', 'description' => 'Порт для Kafka.', 'group' => 'kafka', 'type' => 'integer' ]);
			$this->insert('setting', [ 'name' => 'kafka_topic', 'value' => '', 'description' => 'Название топика для Kafka.', 'group' => 'kafka', 'type' => 'string' ]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'kafka_use', 'kafka_port', 'kafka_topic', 'kafka_host'] ]);
		}
	}