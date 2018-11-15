<?php

	namespace vps\tools\components;

	use Kafka\Consumer;
	use Kafka\ConsumerConfig;
	use Kafka\Producer;
	use Kafka\ProducerConfig;
	use vps\tools\helpers\Console;
	use Yii;
	use yii\base\Component;
	use yii\helpers\Json;

	class Kafka extends Component
	{
		/**
		 * The kafka host.
		 *
		 * @var string|null
		 */
		public $host;
		/**
		 * The kafka port.
		 *
		 * @var string|null
		 */
		public $port;
		/**
		 * This is kafka user which is used to login on the broker.
		 *
		 * @var string|null
		 */
		public $user;
		/**
		 * This is kafka password which is used to login on the broker.
		 *
		 * @var string|null
		 */
		public $password;
		/**
		 * Name topic.
		 *
		 * @var string|null
		 */
		public $topic;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			$this->port = Yii::$app->settings->get('kafka_port');
			$this->user = Yii::$app->settings->get('kafka_user');
			$this->password = Yii::$app->settings->get('kafka_password');
			$this->topic = Yii::$app->settings->get('kafka_topic');
			$this->host = Yii::$app->settings->get('kafka_host');
		}

		/**
		 * @inheritdoc
		 */
		public function sendMessage ($data)
		{
			if (Yii::$app->settings->get('kafka_use'))
			{
				$config = ProducerConfig::getInstance();
				$config->setMetadataRefreshIntervalMs(1000);
				$config->setMetadataBrokerList($this->host . ':' . $this->port);
				$config->setBrokerVersion('2.0.0');
				$config->setRequiredAck(1);
				$config->setIsAsyn(false);
				$config->setProduceInterval(500);

				$producer = new Producer();
			/*	$producer->success(function ($result) use ($data) {
					if (Yii::$app->has('logging'))
						Yii::$app->logging->info(Yii::tr('Данные для {object} отправленны  в Kafka.', [ 'object' => json_encode($data[ 'id' ]) ]));
				});
				$producer->error(function ($errorCode) use ($data) {
					if (Yii::$app->has('logging'))
						Yii::$app->logging->error(Yii::tr('Ошибка {error} отправки сообщения в kafka.'.Json::encode($data), [ 'error' => $errorCode ]));
				});*/
				$producer->send(function () use ($data) {
					return [
						[
							'topic' => $this->topic,
							'value' => Json::encode($data),
							'key'   => '',
						],
					];
				});
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getConsumer ()
		{

			$config = ConsumerConfig::getInstance();
			$config->setMetadataRefreshIntervalMs(1000);
			$config->setMetadataBrokerList($this->host . ':' . $this->port);
			$config->setGroupId(Yii::$app->id);
			$config->setBrokerVersion('2.0.0');
			$config->setTopics([ $this->topic ]);

			$consumer = new Consumer();

			return $consumer;
		}

	}
