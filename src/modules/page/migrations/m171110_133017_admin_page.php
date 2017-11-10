<?php
	use yii\db\Migration;

	/**
	 * Class m171110_132917_admin_page
	 */
	class m171110_133017_admin_page extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->insert('auth_item', [ 'name' => 'admin_page', 'type' => 1, 'created_at' => time(), 'fixed' => 1 ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->delete('auth_item', [ 'name' => 'admin_page' ]);
		}
	}