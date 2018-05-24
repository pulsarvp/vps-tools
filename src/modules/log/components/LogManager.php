<?php

	namespace vps\tools\modules\log\components;

	use vps\tools\modules\log\dictionaries\LogType;
	use vps\tools\modules\log\models\Log;
	use Yii;

	class LogManager extends \yii\base\BaseObject
	{

		private $_use;

		public function init ()
		{
			parent::init();
			$this->_use = Yii::$app->settings->get('log_use');
		}

		public function info ($message, $isRaw = false)
		{
			$this->add($message, LogType::INFO, $isRaw);
		}

		public function error ($message, $isRaw = false)
		{
			$this->add($message, LogType::ERROR, $isRaw);
		}

		public function warning ($message, $isRaw = false)
		{
			$this->add($message, LogType::WARNING, $isRaw);
		}

		private function add ($message, $type, $isRaw)
		{
			if ($this->_use)
			{
				if (!$isRaw)
					$message = Yii::tr($message);
				$log = new Log();
				$log->action = $message;
				$log->type = $type;
				if (isset(Yii::$app->user->id))
				{
					$log->userID = Yii::$app->user->id;
					if (isset(Yii::$app->user->identity->email))
						$log->email = Yii::$app->user->identity->email;
				}
				$log->url = Yii::$app->request->url;
				$log->server = json_encode($_SERVER);
				$log->session = json_encode($_SESSION);
				$log->cookie = json_encode($_COOKIE);
				$log->post = json_encode($_POST);
				$log->save();
			}
		}

	}