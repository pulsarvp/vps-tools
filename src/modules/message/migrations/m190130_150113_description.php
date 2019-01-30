<?php

	use vps\tools\db\Migration;

	class m190130_150113_description extends Migration
	{
		public function safeUp ()
		{
			$this->addColumn('source_message', 'description', $this->text()->null());
		}

		public function safeDown ()
		{
			$this->dropColumn('source_message', 'description');
		}
	}