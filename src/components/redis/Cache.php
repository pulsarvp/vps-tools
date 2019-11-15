<?php

	namespace vps\tools\components\redis;

	use Yii;

	class Cache extends \yii\redis\Cache
	{

		/**
		 * Class Cache
		 *
		 * @package       vps\tools\components\redis
		 *
		 *
		 * ```php
		 *
		 * 'cache'     => [
		 *    'class'            => 'vps\tools\components\redis\Cache',
		 *    'keyPrefixDb'      => 'cache_redis_key_prefix',
		 *    'forceClusterModeDb'       => 'cache_redis_force_cluster',
		 *    ],
		 * ```
		 */
		
		/**
		 * Set keyPrefix from DB settings.
		 * @param string $name
		 */
		public function setKeyPrefixDb ($name)
		{
			$this->keyPrefix = Yii::$app->settings->get($name);
		}

		/**
		 * Set forceClusterMode from DB settings.
		 * @param string $name
		 */
		public function setForceClusterModeDb ($name)
		{
			$item = Yii::$app->settings->get($name);
			if ($item === 1 or $item === true)
				$this->forceClusterMode = true;
			else
				$this->forceClusterMode = false;
		}
	}