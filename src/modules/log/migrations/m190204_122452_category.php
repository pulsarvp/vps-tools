<?php

	use vps\tools\db\Migration;

	class m190204_122452_category extends Migration
	{
		public function safeUp ()
		{
			$this->addColumn('log', 'category', $this->string(255)->null()->after('type'));
			$this->update('log', [ 'category' => 'admin' ]);
		}

		public function safeDown ()
		{
			$this->dropColumn('log', 'category');
		}
	}