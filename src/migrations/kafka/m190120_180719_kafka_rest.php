<?php

	use vps\tools\db\Migration;

	class m190120_180719_kafka_rest extends Migration
	{
		public function safeUp ()
		{
			$this->insert('setting', [ 'name' => 'kafka_rest_host', 'value' => '', 'description' => 'Host для Rest Kafka.', 'group' => 'kafka', 'type' => 'string' ]);
			$this->insert('setting', [ 'name' => 'kafka_rest_port', 'value' => '', 'description' => 'Порт для Rest Kafka.', 'group' => 'kafka', 'type' => 'integer' ]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'kafka_rest_port', 'kafka_rest_host' ] ]);
		}
	}