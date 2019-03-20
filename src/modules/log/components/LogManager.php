<?php

	namespace vps\tools\modules\log\components;

	use vps\tools\modules\log\dictionaries\LogType;
	use vps\tools\modules\log\models\Log;
	use Yii;

	class LogManager extends \yii\base\BaseObject
	{

		public function init ()
		{
			parent::init();
		}

		public static function infoByWeb ($message, $isRaw = false, $category = 'web')
		{
			self::add($message, LogType::INFO, $isRaw, $category);
		}

		public static function info ($message, $isRaw = false, $category = 'admin')
		{
			self::add($message, LogType::INFO, $isRaw, $category);
		}

		public function error ($message, $isRaw = false, $category = 'admin')
		{
			$this->add($message, LogType::ERROR, $isRaw, $category);
		}

		public function warning ($message, $isRaw = false, $category = 'admin')
		{
			$this->add($message, LogType::WARNING, $isRaw, $category);
		}

		private static function add ($message, $type, $isRaw, $category = 'admin')
		{
			if (Yii::$app->settings->get('log_use'))
			{
				if (!$isRaw)
					$message = Yii::tr($message);
				$log = new Log();
				$log->action = $message;
				$log->type = $type;
				$log->category = $category;
				if (isset(Yii::$app->user->id))
				{
					$log->userID = Yii::$app->user->id;
					if (isset(Yii::$app->user->identity->email))
						$log->email = Yii::$app->user->identity->email;
				}
				if (isset(Yii::$app->request->url))
					$log->url = Yii::$app->request->url;
				$log->server = json_encode($_SERVER);
				if (isset($_SESSION))
					$log->session = json_encode($_SESSION);
				$log->cookie = json_encode($_COOKIE);
				$log->post = json_encode($_POST);
				$log->save();
			}
		}

	}