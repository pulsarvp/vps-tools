<?php
	namespace vps\tools\helpers;

	use Yii;

	/**
	 * Class TimeHelper
	 * @package vps\tools\helpers
	 */
	class TimeHelper
	{
		/**
		 * @var string Default datetime format.
		 */
		public static $dtFormat = 'Y-m-d H:i:s';

		/**
		 * @var int Default 'frames per second' value.
		 */
		public static $fps = 25;

		/**
		 * Formats given date in ISO 8601. Expected result is as follow: 2010-03-01T13:10:25+03:00.
		 *
		 * @param integer|string|\DateTime $date The value to be formatted. The following types of value are supported:
		 *                                       - an integer representing a UNIX timestamp,
		 *                                       - a string that can be [parsed to create a DateTime
		 *                                       object](http://php.net/manual/en/datetime.formats.php). The timestamp
		 *                                       is assumed to be in default time zone unless a time zone is explicitly
		 *                                       given.
		 *                                       - a PHP [DateTime](http://php.net/manual/en/class.datetime.php)
		 *                                       object.
		 *                                       * ```php
		 *                                       TimeHelper::cdate('2010-03-01 13:00:00');
		 *                                       ```
		 * @return string The formatted result.
		 * @throws \yii\base\InvalidParamException If the input value can not be evaluated as a date value.
		 */
		public static function cdate ($date)
		{
			return Yii::$app->formatter->asDatetime($date, 'php:c');
		}

		/**
		 * Converts frames to time.
		 * ```php
		 * $result = TimeHelper::fromFrames(214835, 'HH:MM:SS');
		 * ```
		 * @param  integer|string $frames Number of frames.
		 * @return string Time in format HH:MM:SS.MSS.
		 */
		public static function fromFrames ($frames, $format = 'HH:MM:SS.MSS')
		{
			if (is_numeric($frames))
			{
				$h = (int) ( $frames / self::$fps / 3600 );
				$m = (int) ( $frames / self::$fps / 60 - $h * 60 );
				$s = (int) ( $frames / self::$fps - $h * 3600 - $m * 60 );
				$f = $frames - $h * 3600 * self::$fps - $m * 60 * self::$fps - $s * self::$fps;
				$ms = $f * 1000 / self::$fps;

				if ($format == 'HH:MM:SS.MSS')
					return sprintf("%'.02d:%'.02d:%'.02d.%'.03d", $h, $m, $s, $ms);
				elseif ($format == 'HH:MM:SS')
					return sprintf("%'.02d:%'.02d:%'.02d", $h, $m, $s);
				else
					return sprintf("%'.02d:%'.02d:%'.02d:%'.02d", $h, $m, $s, $f);
			}

			return null;
		}

		/**
		 * Converts frames to human readable time.
		 * ```php
		 * $result = TimeHelper::fromFramesToHuman(0, true);
		 * ```
		 * @param  integer|string $frames               Number of frames.
		 * @param boolean         $withLeadingZeroHours If hours are zero should one include them in output or not.
		 * @return string Time in format (HH:)MM:SS.
		 */
		public static function fromFramesToHuman ($frames, $withLeadingZeroHours = false)
		{
			if (is_numeric($frames))
			{
				$h = (int) ( $frames / self::$fps / 3600 );
				$m = (int) ( $frames / self::$fps / 60 - $h * 60 );
				$s = (int) round($frames / self::$fps - $h * 3600 - $m * 60);

				if ($h == 0 and !$withLeadingZeroHours)
					return sprintf("%'.02d:%'.02d", $m, $s);
				else
					return sprintf("%'.02d:%'.02d:%'.02d", $h, $m, $s);
			}

			return null;
		}

		/**
		 * Converts frames to milliseconds.
		 * ```php
		 * $result = TimeHelper::fromFramesToMs(45);
		 * ```
		 * @param  int $frames Number of frames
		 * @return int Number of milliseconds.
		 */
		public static function fromFramesToMs ($frames)
		{
			if (is_numeric($frames))
				return $frames * 1000 / self::$fps;

			return null;
		}

		/**
		 * Converts seconds to human readable time.
		 * ```php
		 * $result = TimeHelper::fromSecondsToHuman(8593.4, true);
		 * ```
		 * @param integer|string $seconds
		 * @param boolean        $withLeadingZeroHours If hours are zero should one include them in output or not.
		 * @return string Time in format (HH:)MM:SS.
		 */
		public static function fromSecondsToHuman ($seconds, $withLeadingZeroHours = false)
		{
			if (is_numeric($seconds))
			{
				$h = (int) ( $seconds / 3600 );
				$m = (int) ( $seconds / 60 - $h * 60 );
				$s = (int) round($seconds - $h * 3600 - $m * 60);

				if ($h == 0 and !$withLeadingZeroHours)
					return sprintf("%'.02d:%'.02d", $m, $s);
				else
					return sprintf("%'.02d:%'.02d:%'.02d", $h, $m, $s);
			}

			return null;
		}

		/**
		 * Return current date and time formatted via [[$dtFormat]].
		 * ```php
		 * $result = TimeHelper::now();
		 * ```
		 * @return string The formatted current date and time.
		 */
		public static function now ()
		{
			return date(self::$dtFormat);
		}

		/**
		 * Converts time to milliseconds.
		 * ```php
		 * $result = TimeHelper::toMs('00:00:01.12');
		 * ```
		 * @param string $time Input time in format HH:MM:SS, HH:MM:SS.FF or HH:MM:SS.MSS.
		 * @return null|integer NUll in case of wrong format input or milliseconds otherwise.
		 */
		public static function toMs ($time)
		{
			// 06:23:16.213
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{3})/', $time, $match);
			if (count($match) == 5)
				return (int) ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 ) * 1000 + $match[ 4 ];

			// 06:23:16.21
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{2})/', $time, $match);
			if (count($match) == 5)
				return ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 ) * 1000 + $match[ 4 ] * 1000 / self::$fps;

			// 06:23:16
			preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $match);
			if (count($match) == 4)
				return ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 ) * 1000;

			return null;
		}

		/**
		 * Converts time to seconds.
		 * ```php
		 * $result = TimeHelper::toSeconds('00:07:02.360');
		 * ```
		 * @param string $time Input time in format HH:MM:SS, HH:MM:SS.FF or HH:MM:SS.MSS.
		 * @return null|integer NUll in case of wrong format input or seconds otherwise.
		 */
		public static function toSeconds ($time)
		{
			// 06:23:16.213
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{3})/', $time, $match);
			if (count($match) == 5)
			{
				if ($match[ 4 ] > 499)
					$match[ 3 ] += 1;

				return (int) ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 );
			}

			// 06:23:16.21
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{2})/', $time, $match);
			if (count($match) == 5)
			{
				if ($match[ 4 ] > self::$fps / 2)
					$match[ 3 ]++;

				return $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600;
			}

			// 06:23:16
			preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $match);
			if (count($match) == 4)
				return $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600;

			return null;
		}

		/**
		 * Converts time to frames.
		 * ```php
		 * $result = TimeHelper::toFrames('00:07:02.360');
		 * ```
		 * @param string $time Input time in format HH:MM:SS, HH:MM:SS.FF or HH:MM:SS.MSS.
		 * @return null|integer NUll in case of wrong format input or frames otherwise.
		 */
		public static function toFrames ($time)
		{
			// 06:23:16.213
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{3})/', $time, $match);
			if (count($match) == 5)
				return (int) ( self::$fps * ( $match[ 4 ] / 1000 + $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 ) );

			// 06:23:16.21
			preg_match('/(\d{2}):(\d{2}):(\d{2})[\.:](\d{2})/', $time, $match);
			if (count($match) == 5)
				return $match[ 4 ] + self::$fps * ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 );

			// 06:23:16
			preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $match);
			if (count($match) == 4)
				return self::$fps * ( $match[ 3 ] + $match[ 2 ] * 60 + $match[ 1 ] * 3600 );

			return null;
		}
	}