<?php
	return [
		'id'         => 'vps-tools-tests',
		'basePath'   => realpath(__DIR__ . '/..'),
		'language'   => 'ru',
		'components' => [
			'category' => [
				'class'      => '\vps\tools\components\CategoryManager',
				'modelClass' => 'tests\models\Category'
			],
			'db'       => require_once __DIR__ . '/db.php',
			'i18n'     => [
				'translations' => [
					'app*' => [
						'class'          => 'yii\i18n\PhpMessageSource',
						'basePath'       => '@tests/messages',
						'sourceLanguage' => 'en',
						'fileMap'        => [
							'app' => 'app.php'
						],
					],
				],
			],
			'session'  => [
				'class' => 'yii\web\Session'
			],
			'settings' => [
				'class'      => '\vps\tools\modules\setting\components\SettingManager',
				'modelClass' => '\vps\tools\modules\setting\models\Setting'
			]
		]
	];