<?php

	use vps\tools\db\Migration;

	class m181214_123807_apiapp_approved extends Migration
	{
		public function safeUp ()
		{
			$this->addColumn('apiapp', 'approved', $this->boolean()->defaultValue(0));
		}

		public function safeDown ()
		{
			$this->dropColumn('apiapp', 'approved');
		}
	}