<?php

	namespace vps\tools\log;

	use Raven_Client;
	use Raven_Stacktrace;
	use Yii;
	use yii\log\Logger;
	use yii\log\Target;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-05-18
	 */
	class SentryTarget extends Target
	{

		/**
		 * @var Raven_Client
		 */
		private $_client;

		/**
		 * @var string Sentry DSN
		 */
		private $_dsn;

		/**
		 * @var string Sentry use
		 */
		private $_use;

		public function init ()
		{
			parent::init();

			$this->_use = Yii::$app->settings->get('sentry_use', 0);
			$this->_dsn = Yii::$app->settings->get('sentry_dsn', '');
			$this->_client = new Raven_Client($this->_dsn);
		}

		/**
		 * Exports log [[messages]] to a specific destination.
		 * Child classes must implement this method.
		 */
		public function export ()
		{
			if ($this->_use)
				foreach ($this->messages as $message)
				{
					list($msg, $level, $category, $timestamp, $traces) = $message;
					$levelName = Logger::getLevelName($level);
					$data = [
						'timestamp' => $timestamp,
						'level'     => $levelName,
						'tags'      => [ 'category' => $category ],
						'message'   => $msg,
					];
					if (!empty($traces))
					{
						$data[ 'sentry.interfaces.Stacktrace' ] = [
							'frames' => Raven_Stacktrace::get_stack_info($traces),
						];
					}
					$this->_client->capture($data, false);
				}
		}
	}