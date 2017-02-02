<?php
	namespace vps\tools\net;

	class CurlResponse extends \yii\base\Object
	{
		const S_UNKNOWN     = 0;
		const S_OK          = 200;
		const S_FORBIDDEN   = 403;
		const S_NOTFOUND    = 404;
		const S_DATAFAILED  = 422;
		const S_SERVERERROR = 500;

		/**
		 * @var array Response headers.
		 */
		private $_headers;

		/**
		 * @var string Response body.
		 */
		private $_body;

		/**
		 * @var int Response HTTP status.
		 */
		private $_status = self::S_UNKNOWN;

		public function __construct (array $config = [])
		{
			if (isset($config[ 'status' ]))
				$this->_status = (int) $config[ 'status' ];

			if (isset($config[ 'body' ]))
				$this->_body = (int) $config[ 'body' ];
		}

		/**
		 * Return response body.
		 *
		 * @return mixed
		 */
		public function getBody ()
		{
			return $this->_body;
		}

		/**
		 * Return response as array. Useful when body is html or xml.
		 *
		 * @return mixed
		 */
		public function getBodyArray ()
		{
			$xml = simplexml_load_string($this->_body);

			return json_decode(json_encode($xml), true);
		}

		/**
		 * Returns response status.
		 *
		 * @return int
		 */
		public function getStatus ()
		{
			if (!$this->_status)
			{
				$this->_status = self::S_UNKNOWN;

				$headers = array_reverse($this->_headers);
				foreach ($headers as $header)
				{
					preg_match("/HTTP\/1.1 (\d{3})/", $header, $match);
					if (isset($match[ 1 ]))
					{
						$this->_status = (int) $match[ 1 ];
						break;
					}
				}
			}

			return $this->_status;
		}

		/**
		 * Parse response from cURL response. Sets body and headers.
		 *
		 * @param $response
		 * @param $curl
		 *
		 * @return CurlResponse
		 */
		public function fromCurl ($response, $curl)
		{
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$this->_body = substr($response, $headerSize);

			$headers = substr($response, 0, $headerSize);
			$headers = str_replace("\r", "", $headers);
			$headers = str_replace("\n\n", "\n", $headers);
			$headers = trim($headers, "\n");
			$this->_headers = explode("\n", $headers);

			return $this;
		}

		/**
		 * Check if status is OK - 2xx.
		 *
		 * @return bool
		 */
		public function isStatusOk ()
		{
			$status = $this->getStatus();

			return ( (int) ( $status / 100 ) == 2 );
		}
	}
