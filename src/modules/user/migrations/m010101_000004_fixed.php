<?php
	use vps\tools\db\Migration;

	class m010101_000004_fixed extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('auth_item', 'fixed', $this->boolean()->defaultValue(0));
			$this->update('auth_item', [ 'fixed' => 1 ], [ 'name' => [ 'admin', 'registered' ] ]);
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('auth_item', 'fixed');
		}
	}