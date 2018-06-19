<?php
	use vps\tools\db\Migration;

	class m010101_100004_groups extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('setting', 'group', $this->string(16)->defaultValue('general'));
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('setting', 'group ');
		}
	}