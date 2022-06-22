<?php

	namespace vps\tools\controllers;

	use vps\tools\helpers\TimeHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\user\filters\AccessControl;
	use Yii;

	class WebController extends \yii\web\Controller
	{
		/**
		 * @var string
		 * Name for the asset bundle.
		 */
		public $assetName = 'app';

		/**
		 * @var \yii\web\AssetBundle
		 */
		protected $_assetBundle;

		/**
		 * @var array
		 * Array to store view data.
		 */
		protected $_data = [ 'error' => [] ];

		/**
		 * @var $string
		 * Path to the view tpl file.
		 */
		protected $_tpl;

		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [],
				],
			];
		}

		/**
		 * Override current template.
		 *
		 * @param string $tpl
		 */
		public function setTemplate ($tpl)
		{
			$this->_tpl = $tpl;
		}

		/**
		 * Set page title.
		 *
		 * @param string $title
		 */
		public function setTitle ($title)
		{
			$this->data('title', $title);
			$this->view->title = $title;
		}

		/**
		 * @inheritdoc
		 */
		public function afterAction ($action, $result)
		{
			$session = Yii::$app->session;
			if ($session->isActive)
				$session->close();

			$this->view->registerAssetBundle($this->assetName);

			if (!isset($this->_data[ 'tpl' ]) or $this->_data[ 'tpl' ] != 'empty.tpl')
				$this->_data[ 'tpl' ] = $this->_tpl . '.tpl';
			$this->forceSetTitle();

			return $this->renderPartial('@app/views/index.tpl', $this->_data);
		}

		/**
		 * @inheritdoc
		 */
		public function beforeAction ($action)
		{
			if (parent::beforeAction($action))
			{
				$session = Yii::$app->session;
				if (!$session->isActive)
					$session->open();

				if (defined('APP_ENV') and APP_ENV == 'deploy' and $action->controller->id != 'env' and $this->checkAccessEnv())
				{
					$this->redirect(Url::toRoute([ '/env' ]));

					return true;
				}

				$this->_assetBundle = Yii::$app->assetManager->getBundle($this->assetName);
				$this->_tpl = $this->id . '/' . $this->action->id;

				if (strpos(Yii::$app->urlManager->hostInfo, Yii::$app->getRequest()->referrer) >= 0 and $this->action->controller->id != 'user')
					Yii::$app->getUser()->setReturnUrl(Yii::$app->getRequest()->referrer);

				$this->on(yii\web\Controller::EVENT_AFTER_ACTION, $this->userActive());

				return true;
			}
			else
				return false;
		}

		/**
		 * Add data to be used in view.
		 *
		 * @param  string $key
		 * @param  mixed  $value
		 */
		public function data ($key, $value)
		{
			$this->_data[ $key ] = $value;
		}

        public function userActive()
        {
            if (isset(Yii::$app->user->id)) {
                /** @var \vps\tools\modules\user\models\User $user */
                $user = Yii::$app->user->identity;
                if ((time() - strtotime($user->activeDT)) > 180) {
                    $user->activeDT = TimeHelper::now();
                    $user->save();
                }
            }
        }

		/**
		 * Add user error message.
		 *
		 * @param  string  $message Message text.
		 * @param  boolean $isRaw Whether given text is raw. If not it will be processed with [[Yii::tr()]].
		 */
		public function error ($message, $isRaw = false)
		{
			Yii::$app->notification->error($message, $isRaw);
		}

		/**
		 * Add user message.
		 *
		 * @param  string  $message Message text.
		 * @param  boolean $isRaw Whether given text is raw. If not it will be processed with [[Yii::tr()]].
		 */
		public function message ($message, $isRaw = false)
		{
			Yii::$app->notification->message($message, $isRaw);
		}

		/**
		 * Redirects and ends app. That prevents from sending additional headers.
		 *
		 * @inheritdoc
		 */
		public function redirect ($url, $statusCode = 302)
		{
			parent::redirect($url, $statusCode);
			Yii::$app->end();
		}

		/**
		 * Add user warning.
		 *
		 * @param  string  $message Message text.
		 * @param  boolean $isRaw Whether given text is raw. If not it will be processed with [[Yii::tr()]].
		 */
		public function warning ($message, $isRaw = false)
		{
			Yii::$app->notification->warning($message, $isRaw);
		}

		/**
		 * Force generate title if not set previously.
		 */
		private function forceSetTitle ()
		{
			if (isset($this->_data[ 'title' ]))
				return;

			$path = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
			if (Yii::$app->has('menu'))
			{
				foreach (Yii::$app->menu as $menu)
				{
					if ($menu->path === $path)
					{
						$this->setTitle($menu->name);

						return;
					}
				}
			}

			$this->setTitle(ucfirst(strtolower(Yii::$app->controller->id . ' ' . Yii::$app->controller->action->id)));
		}

		private function checkAccessEnv ()
		{
			$ip = Yii::$app->getRequest()->getUserIP();
			if (isset(Yii::$app->getModule('debug')->allowedIPsDb))
				$allowedIPsDb = Yii::$app->getModule('debug')->allowedIPsDb;
			else
				$allowedIPsDb = 'debug_allowed_ips';
			$allowedIPs = Yii::$app->settings->get($allowedIPsDb);
			if ($allowedIPs)
				foreach ($allowedIPs as $filter)
				{
					if ($filter === '*' || $filter === $ip || ( ( $pos = strpos($filter, '*') ) !== false && !strncmp($ip, $filter, $pos) ))
					{
						return false;
					}
				}

			return true;
		}
	}
