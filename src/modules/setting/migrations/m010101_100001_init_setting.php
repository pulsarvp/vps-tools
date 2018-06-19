<?php

	use vps\tools\db\Migration;

	class m010101_100001_init_setting extends Migration
	{
		/**
		 * Create table `Setting`
		 */
		public function up ()
		{
			$this->createTable('setting', [
				'name'        => $this->string(45)->notNull()->unique(),
				'value'       => $this->text()->null(),
				'description' => $this->text()->null(),
			]);
			$this->addPrimaryKey('name', 'setting', 'name');
		}

		/**
		 * Drop table `Comment`
		 */
		public function down ()
		{
			$this->dropPrimaryKey('name', 'setting');
			$this->dropTable('setting');
		}
	}