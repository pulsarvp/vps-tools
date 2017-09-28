<?php

	use yii\db\Migration;

	/**
	 * Class m170926_115501_init_menu
	 */
	class m170926_115501_init_menu extends Migration
	{
		/**
		 * Create tables
		 */
		public function up ()
		{
			$tableOptions = null;
			if ($this->db->driverName === 'mysql')
			{
				$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
			}
			$this->createTable('menutype', [
				'id'        => $this->primaryKey(),
				'guid'        => $this->string(128)->notNull()->unique(),
				'title'        => $this->string(255)->null(),
			], $tableOptions);

			$this->createTable('menu', [
				'id'        => $this->primaryKey(),
				'name'        => $this->string(128)->notNull(),
				'url'        => $this->string(128)->null(),
				'path'        => $this->string(128)->null(),
				'lft'        => $this->integer()->null(),
				'rgt'        => $this->integer()->null(),
				'depth'        => $this->integer()->null(),
				'tree'        => $this->integer()->null(),
				'visible'        => $this->boolean()->defaultValue(1),
				'typeID'        => $this->string(255)->null(),
			], $tableOptions);
			$this->createIndex('type','menu','typeID');
			$this->addForeignKey('menu_type','menu','typeID','menutype','id', 'CASCADE','CASCADE');

		}

		/**
		 * Drop tables
		 */
		public function down ()
		{
			$this->dropForeignKey('menu_type','menu');
			$this->dropIndex('type','menu');
			$this->dropTable('menu');
			$this->dropTable('menutype');
		}
	}