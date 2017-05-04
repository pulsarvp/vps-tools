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
			$this->createTable('user', [
				'id'      => $this->primaryKey(),
				'name'    => $this->string(255)->null(),
				'profile' => $this->string(45)->null(),
				'email'   => $this->string(255)->unique()->null(),
				'active'  => $this->boolean()->defaultValue(1),
				'loginDT' => $this->dateTime()->null()
			]);
			$this->insert('setting', [ 'name' => 'text_auth_deny', 'value' => 'Вы отклонили запрос на авторизацию.', 'description' => 'Текст, если пользователь отклонил авторизацию при логине' ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropTable('user');
			$this->delete('setting', [ 'name' => 'text_auth_deny' ]);
		}
	}