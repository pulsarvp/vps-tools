<?php

	use vps\tools\db\Migration;

	class m190617_111552_longtext extends Migration
	{
		public function safeUp ()
		{
			$this->alterColumn('log', 'server', 'LONGTEXT DEFAULT NULL');
			$this->alterColumn('log', 'session', 'LONGTEXT DEFAULT NULL');
			$this->alterColumn('log', 'cookie', 'LONGTEXT DEFAULT NULL');
			$this->alterColumn('log', 'post', 'LONGTEXT DEFAULT NULL');
		}

		public function safeDown ()
		{
		}
	}