<?php
	namespace vps\tools\helpers;

	/**
	 * Class RemoteFileHelper
	 * @package vps\tools\helpers
	 */
	class RemoteFileHelper extends \yii\helpers\BaseFileHelper
	{
		/**
		 * Checks if file is local.
		 * ```php
		 * if(!RemoteFileHelper::isLocal('https://google.com'))
		 * {
		 * }
		 * ```
		 * @param  $path
		 * @return boolean
		 */
		public static function isLocal ($path)
		{
			return file_exists($path);
		}

		/**
		 * Checks whether file exists.
		 * ```php
		 * if(RemoteFileHelper::exists('https://google.com'))
		 * {
		 * }
		 * ```
		 * @param  string $path Path to file
		 * @return boolean Whether file exists.
		 */
		public static function exists ($path)
		{
			if (file_exists($path))
				return true;

			$c = curl_init($path);
			curl_setopt($c, CURLOPT_NOBODY, true);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($c);
			$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
			curl_close($c);

			return ( $code == 200 );
		}

		/**
		 * Saves remote file by chunks.
		 * ```php
		 * if(RemoteFileHelper::save('https://google.com',  'google.html'))
		 * {
		 * }
		 * ```
		 * @param  string  $sourcepath
		 * @param  string  $targetpath
		 * @param  integer $chunksize Chunk size in bytes. Default is 1MB.
		 * @return boolean Whether file is successfully saved.
		 */
		public static function save ($sourcepath, $targetpath, $chunksize = 1048576)
		{
			if (file_exists($targetpath))
				unlink($targetpath);

			if (self::exists($sourcepath))
			{
				$remote = fopen($sourcepath, 'rb');
				$local = fopen($targetpath, 'w');
				while (!feof($remote))
				{
					$data = fread($remote, $chunksize);
					fwrite($local, $data, strlen($data));
				}

				return fclose($remote);
			}

			trigger_error('File path does not exist.', E_USER_WARNING);

			return false;
		}
	}