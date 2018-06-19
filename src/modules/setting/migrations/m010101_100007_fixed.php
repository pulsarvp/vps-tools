<?php

	use vps\tools\db\Migration;

	class m010101_100007_fixed extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('setting', 'fixed', $this->boolean()->defaultValue(0));
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('setting', 'fixed ');
		}
	}