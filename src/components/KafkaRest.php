<?php

	namespace vps\tools\components;

	use vps\tools\helpers\Console;
	use Yii;
	use yii\base\Component;
	use yii\helpers\Json;
	use yii\web\UnprocessableEntityHttpException;

	class KafkaRest extends Component
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
		/**
		 * Use kafka.
		 *
		 * @var string|null
		 */
		public $use;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			$this->host = Yii::$app->settings->get('kafka_rest_host');
			$this->port = Yii::$app->settings->get('kafka_rest_port');
			$this->topic = explode(',', Yii::$app->settings->get('kafka_topic'));
			$this->source = Yii::$app->settings->get('kafka_source');
			$this->use = Yii::$app->settings->get('kafka_use');
		}

		/**
		 * @inheritdoc
		 */
		public function sendMessage ($data)
		{
			if ($this->use)
			{
				try
				{
					$records[ 'records' ][][ 'value' ] = $data;

					// Не через CurlTransport, потому не даёт выставить заголовок Content-Type:application/vnd.kafka.json.v2+json
					// Меняет автоматом на стандартные form или json.
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/topics/' . $this->source);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($records));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$headers = [
						'Content-Type:application/vnd.kafka.json.v2+json'
					];

					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$server_output = curl_exec($ch);

					curl_close($ch);
					$response = json_decode($server_output, true);

					if (isset($response[ 'offsets' ][ 0 ][ 'offset' ]) and $response[ 'offsets' ][ 0 ][ 'offset' ] > 0)
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->info(Yii::tr('Данные для {object} отправленны  в Kafka.', [ 'object' => json_encode($data[ 'id' ]) ]));

						return true;
					}
					else
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->error(Yii::tr('Ошибка {error} отправки сообщения в kafka.' . Json::encode($data), [
								'error' => isset($response[ 'offsets' ][ 0 ][ 'error_code' ]) ? $response[ 'offsets' ][ 0 ][ 'error_code' ] . ' ' . $response[ 'offsets' ][ 0 ][ 'error' ] : ''
							]));

						return false;
					}
				}
				catch (\Exception $e)
				{
					if (YII_DEBUG)
						throw new UnprocessableEntityHttpException($e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getTraceAsString());
					Yii::error($e->getMessage());

					return null;
				}
			}

			return false;
		}

		// Проверка существования потребителя
		public function hasConsumer ()
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source . '/instances/' . $this->source . '/subscription');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$headers = [
				'Content-Type:application/vnd.kafka.v2+json'
			];

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$server_output = curl_exec($ch);
			Console::printColor('Has consumer: ' . $server_output);
			curl_close($ch);
			$response = json_decode($server_output, true);

			if (isset($response[ 'topics' ]) and count($response[ 'topics' ]) > 0)
				return true;
			else
				return false;
		}

		/** Удаления потребителя */
		public function removeConsumer ()
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source . '/instances/' . $this->source);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$headers = [
				'Content-Type:application/vnd.kafka.v2+json'
			];
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$server_output = curl_exec($ch);

			Console::printColor('Remove consumer: ' . $server_output);
			curl_close($ch);
		}

		/** Регистрировании потребителя */
		public function initConsumer ()
		{
			try
			{
				$records[ 'name' ] = $this->source;
				$records[ 'format' ] = 'json';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($records));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Content-Type:application/vnd.kafka.v2+json'
				];
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				Console::printColor('Create consumer: ' . $server_output);
				curl_close($ch);

				$data[ 'topics' ] = $this->topic;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source . '/instances/' . $this->source . '/subscription');
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Content-Type:application/vnd.kafka.v2+json'
				];

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				Console::printColor('Create instances: ' . $server_output);
				curl_close($ch);
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getMessage());

				Yii::error($e->getMessage());
				if (Yii::$app->has('logging'))
					Yii::$app->logging->info(Yii::tr('Kafka init consumers {error}.', [ 'error' => $e->getMessage() ]));

				return [];
			}
		}

		public function read ()
		{
			try
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source . '/instances/' . $this->source . '/records');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Accept:application/vnd.kafka.json.v2+json'
				];

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				curl_close($ch);

				$response = json_decode($server_output, true);

				if (is_array($response) and count($response) > 0 and !isset($response[ 'error_code' ]))
				{
					foreach ($response as $item)
					{
						$this->offsets($item);
					}

					return $response;
				}
				elseif (!empty($response[ 'error_code' ]))
				{
					Console::printColor('Read error code: ' . $server_output);
					$this->removeConsumer();
					$this->initConsumer();

					return [];
				}
				else
				{
					return [];
				}
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getTraceAsString());
				Yii::error($e->getMessage());
				if (Yii::$app->has('logging'))
					Yii::$app->logging->info(Yii::tr('Kafka read message {error}.', [ 'error' => $e->getMessage() ]));

				return [];
			}
		}

		public function offsets ($message)
		{
			try
			{
				if (isset($message[ 'topic' ]))
				{
					$offset = [
						'topic'     => $message[ 'topic' ],
						'partition' => $message[ 'partition' ],
						'offset'    => $message[ 'offset' ] + 1
					];
					$records[ 'offsets' ][] = $offset;

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->source . '/instances/' . $this->source . '/offsets');

					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($records));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$headers = [
						'Content-Type:application/vnd.kafka.v2+json'
					];
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$server_output = curl_exec($ch);

					curl_close($ch);
				}
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getMessage());

				Yii::error($e->getMessage());
				if (Yii::$app->has('logging'))
					Yii::$app->logging->info(Yii::tr('Kafka set offsets {error}.', [ 'error' => $e->getMessage() ]));

				return [];
			}
		}
	}
