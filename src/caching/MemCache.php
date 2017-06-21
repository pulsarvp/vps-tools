<?php

	namespace vps\tools\caching;

	use Yii;

	/**
	 * Class MemCache
	 * @package       vps\tools\components
	 *
	 *
	 * ```php
	 * 'cache'     => [
	 *    'class'             => 'vps\tools\caching\MemCache',
	 *    'enableMemcachedDb' => 'cache_memcached',
	 *    'serverDb'          => [
	 *        'port' => 'cache_port',
	 *        'host' => 'cache_host'
	 *        ]
	 *    ],
	 * ```
	 */
	class MemCache extends \yii\caching\MemCache
	{

		/**
		 * Set cache port and host from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string[] $servers host and port params names
		 */
		public function setServerDb ($servers)
		{
			$this->servers = [
				[
					'port' => Yii::$app->settings->get($servers[ 'port' ]),
					'host' => Yii::$app->settings->get($servers[ 'host' ]),
				]
			];
		}

		/**
		 * Set useMemcached from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setEnableMemcachedDb ($name)
		{
			$this->useMemcached = Yii::$app->settings->get($name);
		}
	}