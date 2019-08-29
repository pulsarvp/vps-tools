<?php

	namespace vps\tools\modules\log\controllers;

	use common\models\User;
	use common\models\UserInfo;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\modules\log\dictionaries\LogType;
	use vps\tools\modules\log\jobs\LogJob;
	use vps\tools\modules\log\models\Log;
	use Yii;
	use yii\console\Controller;
	use yii\data\ActiveDataProvider;
	use yii\queue\redis\Queue;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 208
	 * @date      2018-05-24
	 */
	class LogConsoleController extends Controller
	{

		public function actionSendElk ()
		{
			$logs = Log::find()->orderBy([ 'dt' => SORT_ASC ])->all();
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
				foreach ($logs as $log)
				{
					$result =
						[
							'action'     => $log->action,
							'message'    => $log->action,
							'type'       => $log->type,
							'category'   => $log->category,
							'@timestamp' => strtotime($log->dt),
							'site'       => Yii::$app->id
						];
					$result[ 'user' ][ 'userID' ] = $log->userID;
					if (isset($log->user))
					{
						$result[ 'user' ][ 'email' ] = $log->user->email;
						$result[ 'user' ][ 'untiID' ] = $log->user->info->untiID;
					}

					$result[ 'url' ] = $log->url;

					$result[ 'server' ] = $log->server;

					$result[ 'session' ] = $log->session;
					$result[ 'cookie' ] = $log->cookie;
					$result[ 'post' ] = $log->post;
					fwrite($socket, json_encode($result) . "\r\n");
//					Log::deleteAll([ 'dt' => $log->dt, 'action' => $log->action ]);
				}
			}
			fclose($socket);
		}

	}