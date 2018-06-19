<?php
	use yii\db\Migration;

	/**
	 * Class m171117_124513_type_rules
	 */
	class m010101_000003_type_rules extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('setting', 'type', "ENUM('boolean','command','date','datetime','email','in','integer','ip','json','match','time','string','url') NULL");
			$this->addColumn('setting', 'rule', $this->text()->null());
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('setting', 'rule ');
			$this->dropColumn('setting', 'type');
		}
	}