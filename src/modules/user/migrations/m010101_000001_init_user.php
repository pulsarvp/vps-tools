<?php
	use vps\tools\db\Migration;

	class m010101_000001_init_user extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->createTable('user', [
				'id'      => $this->primaryKey(),
				'name'    => $this->string(128)->null(),
				'profile' => $this->string(45)->null(),
				'email'   => $this->string(128)->unique()->null(),
				'active'  => $this->boolean()->defaultValue(1),
				'loginDT' => $this->dateTime()->null()
			]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropTable('user');
		}
	}