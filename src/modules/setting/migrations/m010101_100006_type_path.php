<?php

	use vps\tools\db\Migration;

	class m010101_100006_type_path extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->alterColumn('setting', 'type', "ENUM('boolean','command','date','datetime','email','in','integer','ip','json','match','time','string','url','path') NULL");
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->alterColumn('setting', 'type', "ENUM('boolean','command','date','datetime','email','in','integer','ip','json','match','time','string','url') NULL");
		}
	}