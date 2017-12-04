<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2017
	 * @date      2017-11-15
	 * @link      https://mega.lectoriy.ru
	 */

	namespace vps\tools\caching;

	use Yii;

	/**
	 * Class RedisCache
	 * @package       common\components
	 *
	 *
	 * ```php
	 *
	 * 'cache'     => [
	 *    'class'            => 'common\caching\RedisCache',
	 *    'keyPrefixDb'      => 'cache_redis_key_prefix',
	 *    'databaseDb'       => 'cache_redis_database',
	 *    'hostnameDb'       => 'cache_redis_hostname',
	 *    'passwordDb'       => 'cache_redis_password',
	 *    'portDb'           => 'cache_redis_port',
	 *    ],
	 * ```
	 */
	class RedisCache extends \yii\redis\Cache
	{

		private $database = 0;
		private $hostname = 'localhost';
		private $password = '';
		private $port     = 6379;

		/**
		 * Set database from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setDatabaseDb ($name)
		{
			$this->database = Yii::$app->settings->get($name);
		}

		/**
		 * Set hostname from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setHostnameDb ($name)
		{
			$this->hostname = Yii::$app->settings->get($name);
		}

		/**
		 * Set password DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setPasswordDb ($name)
		{
			$this->password = Yii::$app->settings->get($name);
		}

		/**
		 * Set port from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setPortDb ($name)
		{
			$this->port = Yii::$app->settings->get($name);
		}

		/**
		 * Set keyPrefix from DB settings.
		 * @see [[\vps\tools\components\SettingManager]]
		 *
		 * @param string $name
		 */
		public function setKeyPrefixDb ($name)
		{
			$this->keyPrefix = Yii::$app->settings->get($name);
		}

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			$params = [
				'port'     => $this->port,
				'hostname' => $this->hostname,
				'database' => $this->database,
			];
			if ($this->password != '')
				$params[ 'password' ] = $this->password;

			$this->redis = $params;
			parent::init();
		}
	}