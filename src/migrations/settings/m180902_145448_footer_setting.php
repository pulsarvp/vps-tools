<?php

	use yii\db\Query;
	use vps\tools\db\Migration;

	class m180902_145448_footer_setting extends Migration
	{
		public function safeUp ()
		{
			$settings = [
				'footer_copyright_from'      => [
					'value'       => 2018,
					'description' => 'Год отсчёта для копирайта.',
					'group'       => 'footer',
					'type'        => 'integer'
				],
				'footer_copyright_org_title' => [
					'value'       => '',
					'description' => 'Заголовок для копирайта.',
					'group'       => 'footer',
					'type'        => 'string'
				],
				'footer_copyright_org_url'   => [
					'value'       => '',
					'description' => 'Ссылка для копирайта.',
					'group'       => 'footer',
					'type'        => 'url'
				],
				'footer_links'               => [
					'value'       => '',
					'description' => 'Ссылки для отображения в футере в формате json.',
					'group'       => 'footer',
					'type'        => 'json'
				],
				'footer_show_version'        => [
					'value'       => 1,
					'description' => 'Показывать ли версию в футере.',
					'group'       => 'footer',
					'type'        => 'boolean'
				]
			];

			foreach ($settings as $name => $setting)
			{
				$n = ( new Query() )->from('setting')->where([ 'name' => $name ])->count();
				if ($n == 0)
				{
					$setting[ 'name' ] = $name;
					$this->insert('setting', $setting);
				}
			}
		}

		public function safeDown ()
		{
		}
	}