<?php

	use vps\tools\db\Migration;

	class m010101_102001_init_queue extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
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