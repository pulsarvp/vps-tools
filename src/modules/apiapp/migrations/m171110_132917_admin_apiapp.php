<?php
	use yii\db\Migration;

	/**
	 * Class m171110_132917_admin_apiapp
	 */
	class m171110_132917_admin_apiapp extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->insert('auth_item', [ 'name' => 'admin_apiapp', 'type' => 1, 'created_at' => time(), 'fixed' => 1 ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->delete('auth_item', [ 'name' => 'admin_apiapp' ]);
		}
	}