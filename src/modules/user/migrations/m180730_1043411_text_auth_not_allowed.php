<?php

	use yii\db\Migration;

	class m180730_1043411_text_auth_not_allowed extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->batchInsert('setting', [ 'name', 'value', 'description', 'type', 'group' ], [
				[ 'text_auth_not_allowed', 'У вас не хватает прав для просмотра этой страницы.', 'Cообщение о том, что не хватает прав.', 'string', 'text' ],
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [
				'name' => [ 'text_auth_not_allowed' ]
			]);
		}
	}