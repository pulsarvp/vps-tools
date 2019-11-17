<?php

	use yii\db\Migration;

	/**
	 * Class m170929_111111_logstash
	 */
	class m171130_084514_logstash extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->insert('setting', [ 'name' => 'logstash_dsn', 'value' => 'tcp://localhost:3333', 'description' => 'DSN Logstash.', 'type' => 'url', 'group' => 'logstash' ]);
			$this->insert('setting', [ 'name' => 'logstash_use', 'value' => '0', 'description' => 'Использовать Logstash.', 'type' => 'boolean', 'group' => 'logstash' ]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [ 'name' => [ 'logstash_dsn', 'logstash_use' ] ]);
		}
	}