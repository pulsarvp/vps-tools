<?php

	use vps\tools\db\Migration;

	class m190116_080126_mailer_secure extends Migration
	{
		public function safeUp ()
		{
			$this->insert('setting', [ 'name' => 'mailer_secure', 'value' => 'ssl', 'description' => 'Хост для отправки писем.', 'type' => 'url', 'group' => 'mailer' ]);
			$this->update('setting', [ 'type' => 'string' ], [ 'name' => 'mailer_host' ]);
		}

		public function safeDown ()
		{
			$this->delete('setting', [ 'name' => [ 'mailer_secure' ] ]);
			$this->update('setting', [ 'type' => 'url' ], [ 'name' => 'mailer_host' ]);
		}
	}