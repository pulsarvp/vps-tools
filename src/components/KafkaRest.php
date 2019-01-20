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
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			$this->host = Yii::$app->settings->get('kafka_rest_host');
			$this->port = Yii::$app->settings->get('kafka_rest_port');
			$this->topic = Yii::$app->settings->get('kafka_topic');
			$this->source = Yii::$app->settings->get('kafka_source');
		}

		/**
		 * @inheritdoc
		 */
		public function sendMessage ($data)
		{
			if (Yii::$app->settings->get('kafka_use'))
			{
				try
				{
					$records[ 'records' ][][ 'value' ] = $data;
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
					$responce = Json::decode($server_output);
					if (isset($responce[ 'offsets' ]))
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->info(Yii::tr('Данные для {object} отправленны  в Kafka.', [ 'object' => json_encode($data[ 'id' ]) ]));

						return true;
					}
					else
					{
						if (Yii::$app->has('logging'))
							Yii::$app->logging->error(Yii::tr('Ошибка {error} отправки сообщения в kafka.' . Json::encode($data), [ 'error' => $errorCode ]));

						return false;
					}
				}
				catch (\Exception $e)
				{
					if (YII_DEBUG)
						throw new UnprocessableEntityHttpException($e->getMessage());
					Yii::error($e->getMessage());

					return null;
				}
			}
		}

		public function initConsumer ()
		{
			try
			{
				$records[ 'name' ] = $this->topic;
				$records[ 'format' ] = 'json';
				$records[ 'auto.offset.reset' ] = 'earliest';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->topic);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($records));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Content-Type:application/vnd.kafka.v2+json'
				];

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				curl_close($ch);
				$responce = Json::decode($server_output);

				$data[ 'topics' ] = [ $this->topic ];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->topic . '/instances/' . $this->topic . '/subscription');
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Content-Type:application/vnd.kafka.v2+json'
				];

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				curl_close($ch);
				$responce = Json::decode($server_output);
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getMessage());
				Yii::error($e->getMessage());

				return [];
			}
		}

		public function read ()
		{
			try
			{

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host . ':' . $this->port . '/consumers/' . $this->topic . '/instances/' . $this->topic . '/records');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$headers = [
					'Accept:application/vnd.kafka.json.v2+json'
				];

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$server_output = curl_exec($ch);

				curl_close($ch);
				$responce = Json::decode($server_output);

				if (is_array($responce) and count($responce) > 0)
				{
					return $responce;
				}
				else
				{
					return [];
				}
			}
			catch (\Exception $e)
			{
				if (YII_DEBUG)
					throw new UnprocessableEntityHttpException($e->getMessage());
				Yii::error($e->getMessage());

				return [];
			}
		}
	}
