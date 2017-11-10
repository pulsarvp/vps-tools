<?php
	use yii\db\Migration;

	/**
	 * Class m171110_132922_admin_menu
	 */
	class m171110_132922_admin_menu extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->insert('auth_item', [ 'name' => 'admin_menu', 'type' => 1, 'created_at' => time(), 'fixed' => 1 ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->delete('auth_item', [ 'name' => 'admin_menu' ]);
		}
	}