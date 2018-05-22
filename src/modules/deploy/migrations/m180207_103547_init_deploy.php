<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */

	use vps\tools\db\Migration;

	/**
	 * Class m171031_115501_init_page
	 */
	class m180207_103547_init_deploy extends Migration
	{
		/**
		 * Create tables
		 */
		public function up ()
		{

			$this->insert('setting', [
				'name'        => 'app_env_deploy_text',
				'value'       => 'Ведутся работы. Скоро вернёмся.',
				'description' => 'Текст для страницы-заглушки при деплое приложения.',
				'type'        => 'string',
				'rule'        => ''
			]);
		}

		/**
		 * Drop tables
		 */
		public function down ()
		{
			$this->delete('setting', [ 'name' => 'app_env_deploy_text' ]);
		}
	}