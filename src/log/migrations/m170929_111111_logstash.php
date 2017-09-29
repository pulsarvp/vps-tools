<?php

	use yii\db\Migration;

	/**
	 * Class m170929_111111_logstash
	 */
	class m170929_111111_logstash extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->insert('setting', [ 'name' => 'logstash_dsn', 'value' => 'tcp://localhost:3333' ]);
			$this->insert('setting', [ 'name' => 'logstash_use', 'value' => '0' ]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [ 'name' => [ 'logstash_dsn', 'logstash_use' ] ]);
		}
	}