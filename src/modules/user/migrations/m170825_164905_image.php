<?php
	use vps\tools\db\Migration;

	class m170825_164905_image extends Migration
	{
		/** @inheritdoc */
		public function up ()
		{
			$this->addColumn('user', 'image', $this->string(255)->null());
		}

		/** @inheritdoc */
		public function down ()
		{
			$this->dropColumn('user', 'image');
		}
	}