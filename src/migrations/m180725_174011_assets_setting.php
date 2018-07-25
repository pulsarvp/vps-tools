<?php

	use yii\db\Migration;

	class m180725_174011_assets_setting extends Migration
	{
		/**
		 * @inheritdoc
		 */
		public function up ()
		{
			$this->batchInsert('setting', [ 'name', 'value', 'description', 'type', 'group' ], [
				[ 'assets_css_use', '0', 'Пользовательский CSS-код. Тэг style использовать не надо.', 'boolean', 'assets' ],
				[ 'assets_css', '', 'Подключать пользовательский CSS-код?', 'string', 'assets' ],
				[ 'assets_js_use', '0', 'Пользовательский JavaScript-код. Тэг script использовать не надо.', 'boolean', 'assets' ],
				[ 'assets_js', '', 'Подключать пользовательский JavaScript-код?', 'string', 'assets' ],
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function down ()
		{
			$this->delete('setting', [
				'name' => [ 'assets_css', 'assets_css_use', 'assets_js', 'assets_js_use' ]
			]);
		}
	}