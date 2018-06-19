<?php

	use yii\db\Migration;

	/**
	 * Class m171117_124513_type_rules
	 */
	class m010101_000006_type_path extends Migration
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