<?php
	namespace vps\tools\net;

	/**
	 * Class Curl
	 *
	 * @package       vps\tools\net
	 * @property-read [[CurlResponse]] $response
	 * @property-write string $returnTransfer
	 */
	class Curl extends \yii\base\Object
	{
		private $_options = [];
		private $_params  = [];
		private $_response;
		private $_headers = [];

		/**
		 * Curl constructor. Sets default options.
		 *
		 * @param string $url URL to be requested.
		 */
		public function __construct ($url)
		{
			$this->_options[ CURLOPT_URL ] = $url;
			$this->_options[ CURLOPT_RETURNTRANSFER ] = 1;
			$this->_options[ CURLOPT_HEADER ] = 1;
			$this->_options[ CURLOPT_USERAGENT ] = 'VPS Curl Request';
		}

		/**
		 * Return request response.
		 *
		 * @return [[CurlResponse]]
		 */
		public function getResponse ()
		{
			return $this->_response;
		}

		/**
		 * Sets option whether to return response transfer or not.
		 *
		 * @param bool $return
		 */
		public function setReturnTransfer ($return)
		{
			$this->_options[ CURLOPT_RETURNTRANSFER ] = (bool) $return;
		}

		/**
		 * Adds header.
		 *
		 * @param string $header
		 * @return Curl
		 */
		public function addHeader ($header)
		{
			$this->_headers[] = $header;

			return $this;
		}

		/**
		 * Adds option.
		 *
		 * @param string $name
		 * @param string $value
		 * @return Curl
		 */
		public function addOption ($name, $value)
		{
			$this->_options[ $name ] = $value;

			return $this;
		}

		/**
		 * Adds param. All params will be appended to request URL.
		 *
		 * @param string $name
		 * @param string $value
		 * @return Curl
		 */
		public function addParam ($name, $value)
		{
			$this->_params[ $name ] = $value;

			return $this;
		}

		/**
		 * Sends DELETE request.
		 *
		 * @param null|array $data Additional data to append to request.
		 * @return string|CurlResponse
		 */
		public function delete ($data = null)
		{
			$this->_options[ CURLOPT_CUSTOMREQUEST ] = 'DELETE';
			if ($data != null)
				$this->_options[ CURLOPT_POSTFIELDS ] = is_array($data) ? http_build_query($data) : $data;

			return $this->send();
		}

		/**
		 * Sends GET request.
		 *
		 * @return string|CurlResponse
		 */
		public function get ()
		{
			return $this->send();
		}

		/**
		 * Sends POST request.
		 *
		 * @param null|array $data Additional data to append to request.
		 * @return string|CurlResponse
		 */
		public function post ($data)
		{
			$this->_options[ CURLOPT_POST ] = 1;
			$this->_options[ CURLOPT_POSTFIELDS ] = http_build_query($data);

			return $this->send();
		}

		/**
		 * Sends PUT request.
		 *
		 * @param null|array $data Additional data to append to request.
		 * @return string|CurlResponse
		 */
		public function put ($data)
		{
			$this->_options[ CURLOPT_CUSTOMREQUEST ] = 'PUT';
			$this->_options[ CURLOPT_POSTFIELDS ] = http_build_query($data);

			return $this->send();
		}

		/**
		 * This is general send method to use in particular request send method.
		 *
		 * @return string|CurlResponse
		 */
		private function send ()
		{
			if (count($this->_params) > 0)
				$this->_options[ CURLOPT_URL ] .= '?' . http_build_query($this->_params);

			if (count($this->_headers) > 0)
				$this->_options[ CURLOPT_HTTPHEADER ] = $this->_headers;

			$curl = curl_init();
			curl_setopt_array($curl, $this->_options);
			$resp = curl_exec($curl);

			if ($resp === false)
			{
				$this->_response = new CurlResponse([
					'status' => CurlResponse::S_SERVERERROR,
					'body'   => 'Error #' . curl_errno($curl) . ': ' . curl_error($curl)
				]);
			}
			else
			{
				$this->_response = new CurlResponse;
				$this->_response->fromCurl($resp, $curl);
			}

			curl_close($curl);

			return $this->_response;
		}

	}
