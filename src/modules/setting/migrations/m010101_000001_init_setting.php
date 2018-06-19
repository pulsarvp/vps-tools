<?php

	use vps\tools\db\Migration;

	/**
	 * Class m010101_100001_init_setting
	 */
	class m010101_000001_init_setting extends Migration
	{
		/**
		 * Create table `Setting`
		 */
		public function up ()
		{
			$tableOptions = null;
			if ($this->db->driverName === 'mysql')
			{
				$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
			}
			$this->createTable('setting', [
				'name'        => $this->string(45)->notNull()->unique(),
				'value'       => $this->text()->null(),
				'description' => $this->text()->null(),
			], $tableOptions);
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