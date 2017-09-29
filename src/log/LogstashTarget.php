<?php

	namespace vps\tools\log;

	use vps\tools\helpers\ArrayHelper;
	use Yii;
	use yii\base\Exception;
	use yii\log\Logger;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-09-29
	 */
	class LogstashTarget extends \yii\log\Target
	{

		/** @var string Connection configuration to Logstash. */
		private $_dsn;

		/**
		 * @var string Logstash use
		 */
		private $_use;

		public function init ()
		{
			parent::init();

			$this->_use = Yii::$app->settings->get('logstash_use', 0);
			if ($this->_use)
			{
				$this->_dsn = Yii::$app->settings->get('logstash_dsn', 'tcp://localhost:3333');
			}
		}

		/**
		 * @inheritdoc
		 */
		public function export ()
		{

			$socket = stream_socket_client($this->_dsn, $errorNumber, $error, 30);

			foreach ($this->messages as &$message)
			{
				list($msg, $level, $category, $timestamp) = $message;

				$user = [];
				try
				{
					if (Yii::$app->user and !Yii::$app->user->isGuest)
						$user = [
							'id'    => Yii::$app->user->id,
							'email' => Yii::$app->user->identity->email,
							'name'  => Yii::$app->user->identity->name
						];
				}
				catch (Exception $e)
				{
				}

				$level = Logger::getLevelName($level);
				$timestamp = date('c', $timestamp);

				$result = ArrayHelper::merge(
					$this->parseText($msg),
					[ 'level'      => $level,
					  'category'   => $category,
					  '@timestamp' => $timestamp,
					  'user'       => $user,
					  'site'       => Yii::$app->id
					]
				);

				if (isset($message[ 4 ]) === true)
				{
					$result[ 'trace' ] = $message[ 4 ];
				}

				fwrite($socket, json_encode($result) . "\r\n");
			}

			fclose($socket);
		}

		/**
		 * Convert's any type of log message to array.
		 *
		 * @param mixed $text Input log message.
		 *
		 * @return array
		 */
		private function parseText ($text)
		{
			$type = gettype($text);
			switch ($type)
			{
				case 'array':
					return $text;
				case 'string':
					return [ '@message' => $text ];
				case 'object':
					return get_object_vars($text);
				default:
					return [ '@message' => \Yii::t('log', "Warning, wrong log message type '{$type}'") ];
			}
		}
	}
