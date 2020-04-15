<?php

	namespace vps\tools\components;

	use RdKafka\Conf;
	use RdKafka\Message;
	use RdKafka\Producer;
	use RdKafka\TopicConf;
	use vps\tools\helpers\Console;
	use Yii;
	use yii\base\Component;
	use yii\helpers\Json;
	use yii\web\UnprocessableEntityHttpException;

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
		public $source;
		/**
		 * Name topic.
		 *
		 * @var string|null
		 */
		public $topic;

		private $_rk;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			$this->port = Yii::$app->settings->get('kafka_port');
			$this->topic = explode(',', Yii::$app->settings->get('kafka_topic'));
			$this->source = Yii::$app->settings->get('kafka_source');
			$this->host = Yii::$app->settings->get('kafka_host');
		}

		/**
		 * @inheritdoc
		 */
		public function sendMessage ($data)
		{
			if (Yii::$app->settings->get('kafka_use'))
			{

				$conf = new \RdKafka\Conf();

				$conf->set('queue.buffering.max.ms', 1);
				$conf->set('queue.buffering.max.messages', 10);
				$conf->set('socket.timeout.ms', 10000);
				$conf->set('retry.backoff.ms', 1000);
				$conf->set('message.send.max.retries', 3);

				$conf->setDrMsgCb(function (Producer $kafka, Message $message) use ($data) {
					if ($message->err)
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->error(Yii::tr('Ошибка {error} отправки сообщения в kafka.' . Json::encode($data), [ 'error' => rd_kafka_err2str($message->err) . ' ' . $message->err ]));
					}
					else
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->info(Yii::tr('Данные для {object} отправленны  в Kafka.', [ 'object' => Json::encode($data) ]));
					}
				});

				$rk = new \RdKafka\Producer($conf);
				$rk->addBrokers($this->host . ':' . $this->port);

				$topicConfig = new TopicConf();
				$topicConfig->set('message.timeout.ms', 2000);

				$kafkaTopic = $rk->newTopic($this->source, $topicConfig);
				try
				{
					$kafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, Json::encode($data));
					$rk->poll(0);
					if(method_exists($rk,'flush'))
                    {
                        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
                            $result = $rk->flush(10000);
                            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                                break;
                            }
                        }
                    }
				}
				catch (\Exception $exception)
				{

                    try
                    {
                        $kafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, Json::encode($data));
                        $rk->poll(0);
                        if(method_exists($rk,'flush'))
                        {
                            for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
                                $result = $rk->flush(10000);
                                if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                                    break;
                                }
                            }
                        }
                    }
                    catch (\Exception $exception)
                    {

                        Yii::error($exception->getMessage());
                        if (Yii::$app->has('logging'))
                            Yii::$app->logging->error(Yii::tr('Ошибка {error} отправки сообщения в kafka.' . Json::encode($data), [ 'error' => $exception->getMessage() ]));

                        return false;
                    }
				}
			}
		}

		public function getTopic ()
		{

			try
			{

				$this->_rk = new \RdKafka\Consumer();
				$this->_rk->setLogLevel(LOG_DEBUG);
				$this->_rk->addBrokers($this->host . ':' . $this->port);
				$queue = $this->_rk->newQueue();
				foreach ($this->topic as $item)
				{
					$topic = $this->_rk->newTopic($item);
					$topic->consumeQueueStart(0, -2, $queue);
				}

				return $queue;
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getTraceAsString());

				Yii::error($e->getMessage());
				if (Yii::$app->has('logging'))
					Yii::$app->logging->error(Yii::tr('Ошибка {error} чтении сообщения из kafka.', [ 'error' => $e->getMessage() ]));

				return null;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getConsumer ()
		{
			return $this->_rk;
		}
	}
