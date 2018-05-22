<?php
	use yii\db\Migration;

	/**
	 * Class m171117_124513_type_rules
	 */
	class m171207_090313_roles extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->insert('auth_item', [ 'name' => 'setting_view', 'description' => 'See setting list', 'type' => 2, 'created_at' => time(), 'fixed' => 1 ]);
			$this->insert('auth_item', [ 'name' => 'setting_edit', 'description' => 'Edit setting', 'type' => 2, 'created_at' => time(), 'fixed' => 1 ]);

			$this->insert('auth_item_child', [ 'parent' => 'admin_setting', 'child' => 'setting_view' ]);
			$this->insert('auth_item_child', [ 'parent' => 'admin_setting', 'child' => 'setting_edit' ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->delete('auth_item', [ 'name' => [ 'setting_view', 'setting_edit' ] ]);
		}
	}