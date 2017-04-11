<?php

	use yii\db\Migration;

	/**
	 * Create table apiapp
	 */
	class m010101_100001_init_apiapp extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$tableOptions = null;
			if ($this->db->driverName === 'mysql')
			{
				$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
			}
			$this->createTable('apiapp', [
				'id'    => $this->primaryKey(),
				'name'  => $this->string(45)->unique()->notNull(),
				'token' => $this->string(32)->unique()->notNull(),
			], $tableOptions);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->dropTable('apiapp');
		}

	}