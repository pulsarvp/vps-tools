<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-07-27
	 * @package   vps\tools\mail
	 */

	namespace vps\tools\mail;

	use Yii;
	use yii\swiftmailer\Mailer;

	/**
	 * Class SwiftMailer
	 * @package vps\tools\mail
	 */
	class SwiftMailer extends Mailer
	{
		/**
		 * @var string A host for sending emails.
		 */
		private $_host;
		/**
		 * @var integer Port for sending letters.
		 */
		private $_port;
		/**
		 * @var string Username for sending emails.
		 */
		private $_username;
		/**
		 * @var string Password for sending emails.
		 */
		private $_password;
		/**
		 * @var \Swift_Transport|array Swift transport instance or its array configuration.
		 */
		private $_transport = [];

		/**
		 * Set client ID.
		 *
		 * @param string $hostDb
		 */
		public function setHostDb ($hostDb)
		{
			$this->_host = Yii::$app->settings->get($hostDb);
		}

		/**
		 * Set client ID.
		 *
		 * @param string $portDb
		 */
		public function setPortDb ($portDb)
		{
			$this->_port = Yii::$app->settings->get($portDb);
		}

		/**
		 * Set client ID.
		 *
		 * @param string $usernameDb
		 */
		public function setUsernameDb ($usernameDb)
		{
			$this->_username = Yii::$app->settings->get($usernameDb);
		}

		/**
		 * Set password.
		 *
		 * @param string $passwordDb
		 */
		public function setPasswordDb ($passwordDb)
		{
			$this->_password = Yii::$app->settings->get($passwordDb);
			$this->getTransport();
		}

		/**
		 * @return array|\Swift_Transport
		 * @throws \yii\base\InvalidConfigException
		 */
		public function getTransport ()
		{
			if (!is_object($this->_transport))
			{
				$this->_transport = $this->createTransport([
					'class'    => 'Swift_SmtpTransport',
					'host'     => $this->_host,
					'port'     => $this->_port,
					'username' => $this->_username,
					'password' => $this->_password
				]);
			}

			return $this->_transport;
		}
	}