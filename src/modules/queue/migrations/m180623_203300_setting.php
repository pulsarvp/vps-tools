<?php
	use vps\tools\db\Migration;

	class m180623_203300_setting extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->update('setting', [
				'type'  => 'integer',
				'group' => 'queue',
				'rule'  => '{"min":1}'
			], [ 'name' => 'queue_ui_pagesize' ]);
		}

		public function down ()
		{
			$this->update('setting', [
				'type'  => null,
				'group' => null,
				'rule'  => null
			], [ 'name' => 'queue_ui_pagesize' ]);
		}
	}