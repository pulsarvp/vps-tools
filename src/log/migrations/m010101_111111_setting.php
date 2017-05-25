<?php

	use yii\db\Migration;

	/**
	 * Class m010101_111111_setting
	 */
	class m010101_111111_setting extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->insert('setting', [ 'name' => 'sentry_dsn', 'value' => '' ]);
			$this->insert('setting', [ 'name' => 'sentry_use', 'value' => '0' ]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [ 'name' => [ 'sentry_dsn', 'sentry_use' ] ]);
		}
	}