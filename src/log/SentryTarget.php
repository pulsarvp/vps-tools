<?php
	namespace vps\tools\log;

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
		 * @var string Sentry DSN
		 */
		public $sentry_dsn;

		/**
		 * @var string Sentry DSN
		 */
		public $sentry_use;

		/**
		 * @var \Raven_Client
		 */
		protected $client;

		public function init ()
		{
			parent::init();

			$this->sentry_use = Yii::$app->settings->get('sentry_use', 0);
			$this->sentry_dsn = Yii::$app->settings->get('sentry_dsn', '');
			$this->client = new \Raven_Client($this->sentry_dsn);
		}

		/**
		 * Exports log [[messages]] to a specific destination.
		 * Child classes must implement this method.
		 */
		public function export ()
		{
			if ($this->sentry_use)
				foreach ($this->messages as $message)
				{
					list( $msg, $level, $category, $timestamp, $traces ) = $message;
					$levelName = Logger::getLevelName($level);
					$data = [
						'timestamp' => gmdate('Y-m-d\TH:i:s\Z', $timestamp),
						'level'     => $levelName,
						'tags'      => [ 'category' => $category ],
						'message'   => $msg,
					];
					if (!empty( $traces ))
					{
						$data[ 'sentry.interfaces.Stacktrace' ] = [
							'frames' => Raven_Stacktrace::get_stack_info($traces),
						];
					}
					$this->client->capture($data, false);
				}
		}
	}