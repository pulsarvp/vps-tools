<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	use vps\tools\db\Migration;

	/**
	 * Class m171031_115501_init_page
	 */
	class m171031_115501_init_page extends Migration
	{
		/**
		 * Create tables
		 */
		public function up ()
		{

			$this->createTable('page', [
				'id'     => $this->primaryKey(),
				'guid'   => $this->string(128)->unique(),
				'title'  => $this->string(128)->notNull(),
				'text'   => $this->text()->notNull(),
				'active' => $this->boolean()->defaultValue(0),
				'dt'     => $this->dateTime()->null()
			]);

			$this->createTable('pagemenu', [
				'pageID' => $this->integer()->notNull(),
				'menuID' => $this->integer()->null(),
			]);
			$this->createIndex('page', 'pagemenu', 'pageID');
			$this->createIndex('menu', 'pagemenu', 'menuID');
			$this->addForeignKey('pagemenu_page', 'pagemenu', 'pageID', 'page', 'id');
			$this->addForeignKey('pagemenu_menu', 'pagemenu', 'menuID', 'menu', 'id', 'SET NULL');
		}

		/**
		 * Drop tables
		 */
		public function down ()
		{
			$this->dropForeignKey('pagemenu_menu', 'pagemenu');
			$this->dropForeignKey('pagemenu_page', 'pagemenu');
			$this->dropIndex('page', 'pagemenu');
			$this->dropIndex('menu', 'pagemenu');
			$this->dropTable('pagemenu');
			$this->dropTable('page');
		}
	}