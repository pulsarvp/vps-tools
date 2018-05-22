<?php
	namespace vps\tools\modules\notification\components;

	use Yii;

	/**
	 * The class for managing notification messages that are displayed to user.
	 *
	 * @property-read Notification[] $data
	 * @property-read Notification[] $errors
	 * @property-read Notification[] $messages
	 * @property-read Notification[] $warnings
	 */
	class NotificationManager extends \yii\base\BaseObject
	{
		/**
		 * The array of notifications.
		 *
		 * @var array
		 */
		private $_data = [];

		/**
		 * Copies all data from other notification manager.
		 *
		 * @param NotificationManager $manager
		 */
		public function copyData ($manager)
		{
			$this->_data = array_merge($this->_data, $manager->getData());
		}

		/**
		 * Copies all errors from other notification manager.
		 *
		 * @param NotificationManager $manager
		 */
		public function copyErrors ($manager)
		{
			$this->_data = array_merge($this->_data, $manager->getErrors());
		}

		/**
		 * Copies all messages from other notification manager.
		 *
		 * @param NotificationManager $manager
		 */
		public function copyMessages ($manager)
		{
			$this->_data = array_merge($this->_data, $manager->getMessages());
		}

		/**
		 * Copies all warnings from other notification manager.
		 *
		 * @param NotificationManager $manager
		 */
		public function copyWarnings ($manager)
		{
			$this->_data = array_merge($this->_data, $manager->getWarnings());
		}

		/**
		 * Initialization method for checking if there are some notification
		 * stored in session. If so the list of notifications is populated and
		 * the list of notifications stored in session is cleared.
		 *
		 * @return void
		 */
		public function init ()
		{
			if (session_status() == PHP_SESSION_NONE)
				session_start();

			if (isset($_SESSION[ 'notification' ]))
			{
				$notification = $_SESSION[ 'notification' ];
				foreach ($notification as $type => $data)
					foreach ($data as $message)
						$this->_data[] = new Notification($message, $type, true);
				$_SESSION[ 'notification' ] = [];
			}
			parent::init();
		}

		/**
		 * Getter for a list of notifications.
		 *
		 * @return array List of notifications.
		 */
		public function getData ()
		{
			return $this->_data;
		}

		/**
		 * Gets all errors.
		 *
		 * @return array
		 */
		public function getErrors ()
		{
			return $this->getNotifications(Notification::ERROR);
		}

		/**
		 * Gets all messages.
		 *
		 * @return array
		 */
		public function getMessages ()
		{
			return $this->getNotifications(Notification::MESSAGE);
		}

		/**
		 * Gets all warnings.
		 *
		 * @return array
		 */
		public function getWarnings ()
		{
			return $this->getNotifications(Notification::WARNING);
		}

		/**
		 * Adds notification of type 'error' to list.
		 *
		 * @param    string  $message Message.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return    void
		 */
		public function error ($message, $isRaw = false)
		{
			$this->add($message, Notification::ERROR, $isRaw);
		}

		/**
		 * Saves notification of type 'error' type to session.
		 *
		 * @param    string  $message Message.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return  void
		 */
		public function errorToSession ($message, $isRaw = false)
		{
			$this->toSession($message, Notification::ERROR, $isRaw);
		}

		/**
		 * Adds notification of type 'message' to list.
		 *
		 * @param    string  $message Message.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return    void
		 */
		public function message ($message, $isRaw = false)
		{
			$this->add($message, Notification::MESSAGE, $isRaw);
		}

		/**
		 * Saves notification of type 'message' type to session.
		 *
		 * @param    string  $message Message.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return    void
		 */
		public function messageToSession ($message, $isRaw = false)
		{
			$this->toSession($message, Notification::MESSAGE, $isRaw);
		}

		/**
		 * Adds notification of type 'warning' to list.
		 *
		 * @param    string  $message Message.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return   void
		 */
		public function warning ($message, $isRaw = false)
		{
			$this->add($message, Notification::WARNING, $isRaw);
		}

		/**
		 * Saves notification of type 'warning' type to session.
		 *
		 * @param  string  $message Message.
		 * @param  boolean $isRaw Whether given message is raw text or should be translated.
		 * @return void
		 */
		public function warningToSession ($message, $isRaw = false)
		{
			$this->toSession($message, Notification::WARNING, $isRaw);
		}

		/**
		 * Adds notification to list.
		 *
		 * @param    string  $message Message.
		 * @param    integer $type Message type.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return    void
		 */
		private function add ($message, $type = Notification::ERROR, $isRaw = false)
		{
			$this->_data[] = new Notification ($message, $type, $isRaw);
		}

		/**
		 * Finds all notifications of given type.
		 *
		 * @param int $type
		 * @return array
		 */
		private function getNotifications ($type)
		{
			$data = [];
			/** @var Notification $notification */
			foreach ($this->_data as $notification)
			{
				if ($notification->type == $type)
					$data[] = $notification;
			}

			return $data;
		}

		/**
		 * Saves notification to session.
		 *
		 * @param    string  $message Message.
		 * @param    integer $type Message type.
		 * @param    boolean $isRaw Whether given message is raw text or should be translated.
		 * @return void
		 */
		private function toSession ($message, $type = Notification::ERROR, $isRaw = false)
		{
			$ntf = new Notification($message, $type, $isRaw);

			$notification = isset($_SESSION[ 'notification' ]) ? $_SESSION[ 'notification' ] : [];
			$notification[ $type ][] = $ntf->message;

			$_SESSION[ 'notification' ] = $notification;
		}
	}