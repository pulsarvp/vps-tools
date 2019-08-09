<?php

	namespace vps\tools\modules\log\jobs;

	use Yii;
	use yii\queue\JobInterface;

	class LogJob implements JobInterface
	{
		public $results;

		public function execute ($queue)
		{
			if (Yii::$app->settings->get('log_ssl_use'))
			{
				$context = stream_context_create(
					[ 'ssl' =>
						  [
							  'local_cert'        => Yii::$app->settings->get('log_elk_cert'),
							  'verify_peer'       => false,
							  'verify_peer_name'  => false,
							  'allow_self_signed' => true,
						  ]
					]
				);
				$socket = stream_socket_client(
					Yii::$app->settings->get('log_elk_dns'),
					$errorNumber,
					$error,
					5,
					STREAM_CLIENT_ASYNC_CONNECT,
					$context);
			}
			else
			{
				$socket = stream_socket_client(Yii::$app->settings->get('log_elk_dns'), $errorNumber, $error, 5, STREAM_CLIENT_ASYNC_CONNECT);
			}
			
			if ($socket)
			{
				fwrite($socket, json_encode($this->results) . "\r\n");
				fclose($socket);
			}
		}

		public function getTtr ()
		{
			return Yii::$app->settings->get('queue_reserve_time', 10);
		}

		public function canRetry ($attempt, $error)
		{
			return $attempt < Yii::$app->settings->get('queue_max_attempt', 1);
		}
	}