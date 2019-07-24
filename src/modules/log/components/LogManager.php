<?php

	namespace vps\tools\modules\log\components;

	use vps\tools\modules\log\dictionaries\LogType;
	use vps\tools\modules\log\models\Log;
	use Yii;

	class LogManager extends \yii\base\BaseObject
	{

		public function init ()
		{
			parent::init();
		}

		public static function errorByWeb ($message, $isRaw = false, $category = 'web')
		{
			self::add($message, LogType::ERROR, $isRaw, $category);
		}

		public static function infoByWeb ($message, $isRaw = false, $category = 'web')
		{
			self::add($message, LogType::INFO, $isRaw, $category);
		}

		public static function info ($message, $isRaw = false, $category = 'admin')
		{
			self::add($message, LogType::INFO, $isRaw, $category);
		}

		public static function error ($message, $isRaw = false, $category = 'admin')
		{
			self::add($message, LogType::ERROR, $isRaw, $category);
		}

		public static function warning ($message, $isRaw = false, $category = 'admin')
		{
			self::add($message, LogType::WARNING, $isRaw, $category);
		}

		private static function add ($message, $type, $isRaw, $category = 'admin')
		{
			if (Yii::$app->settings->get('log_use'))
			{

				if (!$isRaw)
					$message = Yii::tr($message);

				if (Yii::$app->settings->get('log_elk_use'))
				{
					$result =
						[
							'action'     => $message,
							'message'    => $message,
							'type'       => $type,
							'category'   => $category,
							'@timestamp' => time(),
							'site'       => Yii::$app->id
						];

					if (isset(Yii::$app->user->id))
					{
						$result[ 'user' ][ 'userID' ] = Yii::$app->user->id;
						if (isset(Yii::$app->user->identity->email))
							$result[ 'user' ][ 'email' ] = Yii::$app->user->identity->email;
						if (isset(Yii::$app->user->identity->info->untiID))
							$result[ 'user' ][ 'untiID' ] = Yii::$app->user->identity->info->untiID;

						if (isset(Yii::$app->request->url))
							$result[ 'url' ] = Yii::$app->request->url;

						$result[ 'server' ] = $_SERVER;
						if (isset($_SESSION))
							$result[ 'session' ] = $_SESSION;
						$result[ 'cookie' ] = $_COOKIE;
						$result[ 'post' ] = $_POST;

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
							$socket =
								stream_socket_client(
									Yii::$app->settings->get('log_elk_dns'),
									$errorNumber,
									$error,
									30,
									STREAM_CLIENT_CONNECT,
									$context);
						}
						else
						{
							$socket = stream_socket_client(Yii::$app->settings->get('log_elk_dns'), $errorNumber, $error, 30);
						}

						if ($socket)
						{
							var_dump(fwrite($socket, json_encode($result) . "\r\n"));

							fclose($socket);
						}
					}
				}
				else
				{
					$log = new Log();
					$log->action = $message;
					$log->type = $type;
					$log->category = $category;
					if (isset(Yii::$app->user->id))
					{
						$log->userID = Yii::$app->user->id;
						if (isset(Yii::$app->user->identity->email))
							$log->email = Yii::$app->user->identity->email;
					}
					if (isset(Yii::$app->request->url))
						$log->url = Yii::$app->request->url;
					$log->server = json_encode($_SERVER);
					if (isset($_SESSION))
						$log->session = json_encode($_SESSION);
					$log->cookie = json_encode($_COOKIE);
					$log->post = json_encode($_POST);
					$log->save();
				}
			}
		}

	}