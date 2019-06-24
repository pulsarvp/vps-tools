<?php

	namespace vps\tools\components\redis;

	use Yii;

	class Queue extends \yii\queue\redis\Queue
	{
		public $ttr = 86400;

		/**
		 * Set channel from DB settings.
		 * @param string $value
		 */
		public function setChannelDb ($value)
		{
			$this->channel = Yii::$app->settings->get($value);
		}
	}