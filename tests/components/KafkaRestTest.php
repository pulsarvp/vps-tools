<?php

	namespace tests\components;

	use vps\tools\components\KafkaRest;
	use vps\tools\helpers\Console;
	use Yii;
	use yii\base\Theme;
	use yii\helpers\Json;

	class KafkaRestTest extends \PHPUnit\Framework\TestCase
	{
		private $_kafka;

		public function setUp ()
		{
			parent::setUp();
			$this->_kafka = new KafkaRest();
			$config = [];
		}

		public function testSendAndRead ()
		{

			if (file_exists(__DIR__ . '/../config/kafka.php'))
			{
				$config = require __DIR__ . '/../config/kafka.php';
			}
			else
			{
				$config[ 'use' ] = 1;
				$config[ 'host' ] = Console::prompt('Host kafka:');
				$config[ 'port' ] = Console::prompt('Port kafka:');
				$config[ 'topic' ] = Console::prompt('Topic read kafka:');
				$config[ 'source' ] = Console::prompt('Topic send kafka:');
			}
			$this->_kafka->use = $config[ 'use' ];
			$this->_kafka->host = $config[ 'host' ];
			$this->_kafka->port = $config[ 'port' ];
			$this->_kafka->topic = $config[ 'topic' ];
			$this->_kafka->source = $config[ 'source' ];

			$data = [
				'action'    => 'test_kafka',
				'type'      => 'unittest',
				'timestamp' => date('c'),
				'source'    => 'test_kafka',
				'title'     => 'Test kafka rest',
				'id'        => '0',
			];
			$this->assertTrue($this->_kafka->sendMessage($data));

			$this->_kafka->initConsumer();
			$flag = true;
			while ($flag)
			{
				sleep(1);
				$messages = $this->_kafka->read();
				if (count($messages))
				{
					foreach ($messages as $message)
					{
						if (isset($message[ 'value' ][ 'source' ]))
						{

							$this->assertEquals($data, $message[ 'value' ]);
							$this->assertArrayHasKey('action', $message[ 'value' ]);
							$this->assertEquals($message[ 'value' ][ 'action' ], 'test_kafka');
							$flag = false;
						}
					}
				}
			}
		}
	}