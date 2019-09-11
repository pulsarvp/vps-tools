<?php

	use vps\tools\db\Migration;

	class m190911_101340_action_longtext extends Migration
	{
		public function safeUp ()
		{
			$this->alterColumn('log', 'action', 'LONGTEXT');
		}

		public function safeDown ()
		{
		}
	}