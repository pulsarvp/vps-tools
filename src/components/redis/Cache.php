<?php

	namespace vps\tools\components\redis;

	use Yii;

	class Cache extends \yii\redis\Cache
	{
		/**
		 * Set keyPrefix from DB settings.
		 * @param string $name
		 */
		public function setKeyPrefixDb ($name)
		{
			$this->keyPrefix = Yii::$app->settings->get($name);
		}
	}