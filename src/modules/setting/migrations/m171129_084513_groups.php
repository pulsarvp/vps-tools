<?php
	use yii\db\Migration;

	/**
	 * Class m171117_124513_type_rules
	 */
	class m171129_084513_groups extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('setting', 'group', $this->string(16)->defaultValue('general'));
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('setting', 'group ');
		}
	}