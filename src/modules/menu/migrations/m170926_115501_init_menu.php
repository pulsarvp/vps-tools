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
			$this->createTable('menutype', [
				'id'        => $this->primaryKey(),
				'guid'        => $this->string(128)->notNull()->unique(),
				'title'        => $this->string(255)->null(),
			]);

			$this->createTable('menu', [
				'id'        => $this->primaryKey(),
				'title'        => $this->string(128)->notNull(),
				'url'        => $this->string(128)->null(),
				'path'        => $this->string(128)->null(),
				'lft'        => $this->integer()->notNull(),
				'rgt'        => $this->integer()->notNull(),
				'depth'        => $this->integer()->notNull(),
				'tree'        => $this->integer()->null(),
				'visible'        => $this->boolean()->defaultValue(1),
				'typeID'        => $this->integer()->notNull(),
			]);
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