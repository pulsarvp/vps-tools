<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2019-01-16
	 * @package   vps\tools\mail
	 */

	namespace vps\tools\mail;

	use Yii;
	use yii\base\Component;
	use zyx\phpmailer\Mailer;

	/**
	 * Class PhpMailer
	 * @package vps\tools\mail
	 */
	class PhpMailer extends Mailer
	{

		public $transport;
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
		 * @var string Stmpsecure for sending emails.
		 */
		private $_secure = 'ssl';

		/**
		 * @var \Swift_Transport|array Swift transport instance or its array configuration.
		 */
		public function init ()
		{
			$this->config = [
				'mailer'   => 'smtp',
				'host'     => $this->_host,
				'port'     => $this->_port,
				'secure'   => $this->_secure,
				'username' => $this->_username,
				'password' => $this->_password
			];
			parent::init();
		}

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
		}

		/**
		 * Set SMTPSecure.
		 *
		 * @param string $secureDb
		 */
		public function setSecureDb ($passwordDb)
		{
			$this->_secure = Yii::$app->settings->get($passwordDb);
		}

	}