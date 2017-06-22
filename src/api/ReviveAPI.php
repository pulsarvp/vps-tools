<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-06-22
	 * @package   vps\tools\api
	 */

	namespace vps\tools\api;

	use Yii;

	/**
	 * Class ReviveAPI
	 */
	class ReviveAPI extends \yii\base\Object
	{
		private $_url;
		private $_sessionID;

		public function init ()
		{
			$this->_url = Yii::$app->settings->get('banner_api_url');
			$this->_login();
		}

		/**
		 * Get Agency list
		 *
		 * @return array
		 */
		public function getAgencies ()
		{
			return $this->_send('ox.getAgencyList', [ $this->_sessionID ]);
		}

		/**
		 * Get Users list
		 * @return array
		 */
		public function getUsers ()
		{
			return $this->_send('ox.getUserList', [ $this->_sessionID ]);
		}

		private function _login ()
		{
			$this->_sessionID = $this->_send('ox.logon', [ Yii::$app->settings->get('banner_api_login'), Yii::$app->settings->get('banner_api_password') ]);
		}

		/**
		 * Send request
		 *
		 * @param string $method
		 * @param array  $params
		 *
		 * @return mixed
		 */
		private function _send (string $method, array $params)
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