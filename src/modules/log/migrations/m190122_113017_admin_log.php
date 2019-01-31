<?php

	use yii\db\Migration;

	/**
	 * Class m190122_113017_admin_log
	 */
	class m190122_113017_admin_log extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->insert('auth_item', [ 'name' => 'admin_log', 'type' => 1, 'created_at' => time(), 'fixed' => 1 ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->delete('auth_item', [ 'name' => 'admin_log' ]);
		}
	}