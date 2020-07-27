<?php

	namespace vps\tools\components\redis;

	use Yii;
	use yii\db\Exception;

	class Connection extends \yii\redis\Connection
	{
		/**
		 * Set database from DB settings.
		 * @param string $name
		 */
		public function setDatabaseDb ($name)
		{
			$this->database = Yii::$app->settings->get($name);
		}

		/**
		 * Set hostname from DB settings.
		 * @param string $name
		 */
		public function setHostnameDb ($name)
		{
			$this->hostname = Yii::$app->settings->get($name);
		}

		/**
		 * Set password DB settings.
		 * @param string $name
		 */
		public function setPasswordDb ($name)
		{
			$value = Yii::$app->settings->get($name);

			if (empty($value))
			{
				return;
			}

			$this->password = $value;
		}

		/**
		 * Set port from DB settings.
		 * @param string $name
		 */
		public function setPortDb ($name)
		{
			$this->port = Yii::$app->settings->get($name);
		}

		public function executeCommand ($name, $params = [])
		{

			if (defined(YII_DEBUG) and YII_DEBUG)
				return parent::executeCommand($name, $params);
			else
				try
				{
					return parent::executeCommand($name, $params);
				}
				catch (Exception $e)
				{
					return null;
				}
		}
	}