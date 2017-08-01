<?php
	use vps\tools\db\Migration;

	/**
	 * Class m010202_200002_init_user
	 */
	class m010202_200002_init_user extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$tableOptions = null;
			if ($this->db->driverName === 'mysql')
			{
				$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
			}
			$this->createTable('user', [
				'id'      => $this->primaryKey(),
				'name'    => $this->string(128)->null(),
				'profile' => $this->string(45)->null(),
				'email'   => $this->string(128)->unique()->null(),
				'active'  => $this->boolean()->defaultValue(1),
				'loginDT' => $this->dateTime()->null()
			], $tableOptions);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropTable('user');
		}
	}