<?php
	use yii\db\Migration;

	/**
	 * Class m171005_144905_auth_item
	 */
	class m171005_144905_fixed extends Migration
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