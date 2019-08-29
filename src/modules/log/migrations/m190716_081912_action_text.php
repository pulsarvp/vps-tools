<?php

	use vps\tools\db\Migration;

	class m190716_081912_action_text extends Migration
	{
		public function safeUp ()
		{
			$this->alterColumn('log', 'action', $this->text()->null());
		}

		public function safeDown ()
		{
			$this->alterColumn('log', 'action', $this->string(255)->null());
		}
	}