<?php

	use vps\tools\db\Migration;

	class m180528_134502_activeDT extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('user', 'activeDT', $this->dateTime()->null());
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('user', 'activeDT');
		}
	}