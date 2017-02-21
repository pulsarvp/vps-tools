<?php
	namespace vps\tools\helpers;

	use Yii;

	/**
	 * Class FFmpegHelper
	 * @package vps\tools\helpers
	 */
	class FFmpegHelper
	{
		/**
		 * Gets video file info. Filters parameters with $names keys if necessary.
		 * ```php
		 * $result = FFmpegHelper::info('/path/to/the/file.mp4', [ 'duration', 'bitrate' ]);
		 * ```
		 *
		 * @param string     $path
		 * @param null|array $names
		 * @return array
		 */
		public static function info ($path, $names = null)
		{
			$data = [];

			$ffprobe = self::binpath('ffprobe');
			exec($ffprobe . ' ' . escapeshellarg($path) . ' -show_format -show_streams -v quiet', $format);

			$info = [];
			foreach ($format as $line)
			{
				preg_match("/(.*)\=(.*)/", $line, $match);
				if (count($match) == 3)
					$info[ $match[ 1 ] ] = $match[ 2 ];
			}

			if (is_array($names))
			{
				foreach ($names as $name)
				{
					$data[ $name ] = $info[ $name ] ?? null;
				}
			}
			else
				$data = $info;

			return $data;
		}

		/**
		 * Finds path to binary executable file.
		 * ```php
		 * $path = FFmpegHelper::binpath('ffprobe');
		 * ```
		 *
		 * @param string $name
		 * @return null|string
		 */
		private static function binpath ($name = 'ffprobe')
		{
			$path = null;

			if (Yii::$app->has('settings'))
				$path = Yii::$app->settings->get($name . 'path');

			if ($path == null)
				$path = trim(shell_exec('which ' . $name));

			if ($path == null)
			{
				$pathenvs = explode(':', getenv('PATH'));
				foreach ($pathenvs as $pathenv)
					if (file_exists($pathenv . '/' . $name) and is_executable($pathenv . '/' . $name))
						return $pathenv . '/' . $name;
			}

			return $path;
		}
	}