<?php

	use vps\tools\db\Migration;
	use \yii\db\Query;

	class m170302_140341_init_queue extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$n = ( new Query() )
				->from('setting')
				->where([ 'name' => 'queue_ui_pagesize' ])
				->count();

			if ($n == 0)
				$this->insert('setting', [
					'name'        => 'queue_ui_pagesize',
					'value'       => '20',
					'description' => 'Интерфейс для очередей: количество объектов на одной странице.'
				]);
		}

		public function down ()
		{
			$this->delete('setting', [ 'name' => [ 'queue_ui_pagesize' ] ]);
		}
	}