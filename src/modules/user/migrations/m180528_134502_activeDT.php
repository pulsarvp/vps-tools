<?php

	use yii\db\Migration;

	/**
	 * Class m180528_134502_activeDT
	 */
	class m180528_134502_activeDT extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('user', 'activeDT', $this->dateTime()->null());
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('user', 'activeDT');
		}
	}