<?php

	namespace vps\tools\modules\notification\components;

	use Yii;

	/**
	 * The class for holding notification objects that are displayed to user.
	 *
	 * @property-read string $message
	 * @property-read string $type
	 * @property-read string $class
	 */
	class Notification extends \yii\base\BaseObject
	{
		const ERROR   = 0;
		const WARNING = 1;
		const MESSAGE = 2;

		private $_canHide = true;
		/**
		 * Category for translation.
		 *
		 * @var string
		 */
		private $_category = 'app';

		/**
		 * Message text.
		 *
		 * @var string
		 */
		private $_message = '';

		/**
		 * Type of the message.
		 *
		 * @var integer
		 */
		private $_type = self::ERROR;

		// Getters and setters.
		public function getMessage () { return $this->_message; }

		public function getCanHide () { return $this->_canHide; }

		public function getType () { return $this->_type; }

		public function getClass ()
		{
			switch ($this->_type)
			{
				case self::ERROR:
					return 'danger';
				case self::WARNING:
					return 'warning';
				case self::MESSAGE:
					return 'info';
				default:
					return 'default';
			}
		}

		public function setCategory ($category) { $this->_category = $category; }
		// end Getters and setters.

		/**
		 * Creating new notification.
		 *
		 * @param string  $message Message.
		 * @param integer $type    Message type.
		 * @param boolean $isRaw   Whether given message is raw text or should be translated.
		 */
		public function __construct ($message, $type = self::ERROR, $isRaw = false, $canHide = true)
		{
			$this->_type = $type;
			$this->_canHide = $canHide;

			if ($isRaw)
			{
				$this->_message = $message;
			}
			else
			{
				$this->_message = Yii::t($this->_category, $message);
			}
		}
	}