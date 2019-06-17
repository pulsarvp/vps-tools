<?php

	use vps\tools\db\Migration;

	class m190617_132506_page_text_longtext extends Migration
	{
		public function safeUp ()
		{
			$this->alterColumn('page', 'text', 'LONGTEXT');
		}

		public function safeDown ()
		{
			$this->alterColumn('page', 'text', $this->text());
		}
	}