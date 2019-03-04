<?php

	use vps\tools\db\Migration;

	class m180727_090054_mailer_settings_type extends Migration
	{
		public function safeUp ()
		{
			$this->update('setting', [ 'type' => 'string', 'group' => 'mailer' ], [ 'name' => 'mailer_host' ]);
			$this->update('setting', [ 'type' => 'integer', 'group' => 'mailer', 'rule' => '{"min":0}' ], [ 'name' => 'mailer_port' ]);
			$this->update('setting', [ 'type' => 'string', 'group' => 'mailer' ], [ 'name' => 'mailer_username' ]);
			$this->update('setting', [ 'type' => 'string', 'group' => 'mailer' ], [ 'name' => 'mailer_password' ]);
		}

		public function safeDown ()
		{

		}
	}