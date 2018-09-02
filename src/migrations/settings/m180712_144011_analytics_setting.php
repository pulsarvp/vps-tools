<?php

	use yii\db\Migration;

	class m180712_144011_analytics_setting extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->batchInsert('setting', [ 'name', 'value', 'description', 'type', 'group' ], [
				[ 'analytics_google_use', '0', 'Использовать гугл-аналитику.', 'boolean', 'analytics' ],
				[ 'analytics_google_key', '', 'Ключ для гугл-аналитики.', 'string', 'analytics' ],
				[ 'analytics_yandex_use', '0', 'Использовать яндекс-метрику.', 'boolean', 'analytics' ],
				[ 'analytics_yandex_key', '', 'Ключ для яндекс-метрики.', 'string', 'analytics' ],
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [
				'name' => [ 'analytics_google_use', 'analytics_google_key', 'analytics_yandex_use', 'analytics_yandex_key' ]
			]);
		}
	}