<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-06-22
	 * @package   vps\tools\api
	 */

	namespace vps\tools\api;

	use Yii;
	use yii\base\InvalidConfigException;
	use yii\web\NotFoundHttpException;

	/**
	 * Class ReviveAPI
	 */
	class ReviveAPI extends \yii\base\Object
	{
		private $_url;
		private $_login;
		private $_password;
		private $_sessionID;

		public function init ()
		{
			if (Yii::$app->settings->get('banner_use'))
			{
				$this->_url = Yii::$app->settings->get('banner_api_url');
				$this->validateUrl();
				$this->_login = Yii::$app->settings->get('banner_api_login');
				$this->_password = Yii::$app->settings->get('banner_api_password');
				$this->login();
			}
			else
				throw new InvalidConfigException(Yii::tr('Banners are not enabled.'));
		}

		/**
		 * Get Agency list
		 *
		 * @return array
		 */
		public function getAgencies ()
		{
			return $this->send('ox.getAgencyList', [ $this->_sessionID ]);
		}

		/**
		 * Get Users list
		 * @return array
		 */
		public function getUsers ()
		{
			return $this->send('ox.getUserList', [ $this->_sessionID ]);
		}

		private function validateUrl ()
		{
			if (filter_var($this->_url, FILTER_VALIDATE_URL) === false)
				throw new InvalidConfigException(Yii::tr('The must be Url.'));
		}

		private function login ()
		{
			$this->_sessionID = $this->send('ox.logon', [ $this->_login, $this->_password ]);
			if (is_array($this->_sessionID))
				throw new NotFoundHttpException($this->_sessionID[ 'faultString' ]);
		}

		/**
		 * Send request
		 *
		 * @param string $method
		 * @param array  $params
		 *
		 * @return mixed
		 */
		private function send (string $method, array $params)
		{

			$request = xmlrpc_encode_request($method, $params);

			$context = stream_context_create([ 'http' => [
				'method'  => "GET",
				'header'  => "Content-Type: text/xml",
				'content' => $request
			] ]);

			$file = file_get_contents($this->_url, false, $context);

			return xmlrpc_decode($file);
		}
	}