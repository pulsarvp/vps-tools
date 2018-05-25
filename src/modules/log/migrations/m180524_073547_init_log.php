<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-05-24
	 */

	use vps\tools\db\Migration;

	/**
	 * Class m180524_073547_init_log
	 */
	class m180524_073547_init_log extends Migration
	{
		/**
		 * Create tables
		 */
		public function up ()
		{
			$this->createTable('log', [
				'userID'  => $this->integer()->null(),
				'email'   => $this->string(255)->null(),
				'type'    => 'ENUM("error","info","warning") DEFAULT "info"',
				'action'  => $this->string(255)->null(),
				'url'     => $this->string(1000)->null(),
				'server'  => $this->text()->null(),
				'session' => $this->text()->null(),
				'cookie'  => $this->text()->null(),
				'post'    => $this->text()->null(),
				'dt'      => $this->dateTime()->null(),
			]);
			$this->insert('setting', [
				'name'        => 'log_use',
				'value'       => '1',
				'description' => 'Использовать систему логирования.',
				'type'        => 'boolean',
				'rule'        => '',
				'group'       => 'log'
			]);
		}

		/**
		 * Drop tables
		 */
		public function down ()
		{
			$this->delete('setting', [ 'name' => 'log_use' ]);
			$this->dropTable('log');
		}
	}