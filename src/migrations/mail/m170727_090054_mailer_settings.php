<?php

	use vps\tools\db\Migration;

	class m170727_090054_mailer_settings extends Migration
	{
		public function safeUp()
		{
			$this->insert('setting', [ 'name' => 'mailer_host', 'value' => 'localhost', 'description' => 'Хост для отправки писем.' ]);
			$this->insert('setting', [ 'name' => 'mailer_port', 'value' => '25', 'description' => 'Порт для отправки писем.' ]);
			$this->insert('setting', [ 'name' => 'mailer_username', 'value' => '', 'description' => 'Имя пользователя для отправки писем.' ]);
			$this->insert('setting', [ 'name' => 'mailer_password', 'value' => '', 'description' => 'Пароль для отправки писем.' ]);
		}

		public function safeDown()
		{
			$this->delete('setting', [ 'name' => [ 'mailer_host', 'mailer_port', 'mailer_username', 'mailer_password'] ]);
		}
	}