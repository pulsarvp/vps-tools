<?php
	namespace vps\tools\helpers;

	/**
	 * Class StringHelper
	 *
	 * @package vps\tools\helpers
	 */
	class StringHelper extends \yii\helpers\BaseStringHelper
	{
		/**
		 * Removes all characters from string except letters, digits, underscore, hyphen and whitespace characters.
		 * ```php
		 * $result = StringHelper::clear("{}test  (* asd");
		 * // $result will be: 'test   asd'
		 * ```
		 *
		 * @param string $str string
		 * @return string converted string
		 */
		public static function clear ($str)
		{
			return preg_replace('/[^\s\d\w\-]/u', '', $str);
		}

		/**
		 * Overrides parent method with $skipEmpty default value set to true.
		 * ```php
		 * $result = StringHelper::explode("It+ is+ a second+ test", '+');
		 * // $result will be:
		 * // [ 'It', 'is', 'a second', 'test' ]
		 * ```
		 *
		 * @inheritdoc
		 */
		public static function explode ($string, $delimiter = ',', $trim = true, $skipEmpty = true)
		{
			return parent::explode($string, $delimiter, $trim, $skipEmpty);
		}

		/**
		 * Explodes string with multiple delimiters.
		 * ```php
		 * $result = StringHelper::mexplode('sd:ds*da:adsad adsad;cs', [ ':', ';', ' ', 'a', '*' ]);
		 * // $result will be:
		 * // [ 'sd', 'ds', 'd', 'ds', 'd', 'ds', 'd', 'cs' ]
		 * ```
		 *
		 * @param string   $string
		 * @param string[] $delimiters
		 * @return array|null
		 */
		public static function mexplode ($string, $delimiters = [ ',' ])
		{
			if (is_string($delimiters) or is_numeric($delimiters))
				$delimiters = [ $delimiters ];

			if (!is_array($delimiters))
				return null;

			return preg_split('/[' . implode('', $delimiters) . ']+/', $string, -1, PREG_SPLIT_NO_EMPTY);
		}

		/**
		 * Gets the position of nth occurrence of character.
		 * ```php
		 * $result = StringHelper::pos('lakanahbahakjlapaosa', 'a', 5);
		 * // $result will be:
		 * // 10
		 * ```
		 *
		 * @param  string  $string String to be searched for character.
		 * @param  string  $char
		 * @param  integer $n Which occurrence to search for. If negative then character will be searched from the end of string.
		 * @return integer|null Nth occurrence (if existed), null otherwise.
		 * @see rpos
		 */
		public static function pos ($string, $char, $n = 1)
		{
			if ($n < 0)
				return self::rpos($string, $char, -$n);

			$count = mb_substr_count($string, $char);
			if ($n > $count)
				return null;

			$p = -1;
			for ($i = 0; $i < $n; $i++)
				$p = mb_stripos($string, $char, $p + 1);

			return $p;
		}

		/**
		 * Generates random string from latin letters and numbers.
		 * ```php
		 * $result = StringHelper::random(15, true);
		 * // $result will be:
		 * // NdSa6w7As8E9fa5
		 * ```
		 *
		 * @param int     $length Desired string length.
		 * @param boolean $upper Whether use also upper letters.
		 * @return string|null Generated string.
		 */
		public static function random ($length = 10, $upper = false)
		{
			if (is_numeric($length) and $length > 0)
			{
				$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
				if ($upper)
					$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

				return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
			}

			return null;
		}

		/**
		 * Gets the position of nth occurrence of character from the end of string.
		 * ```php
		 * $result = StringHelper::rpos('lakanahbahakjlapaosa', 'a', -5);
		 * // $result will be:
		 * // 10
		 * ```
		 *
		 * @param  string  $string String to be searched for character.
		 * @param  string  $char
		 * @param  integer $n Which occurrence to search for. If negative then character will be searched from the begin of string.
		 * @return integer|null Nth occurrence (if existed), null otherwise.
		 * @see pos
		 */
		public static function rpos ($string, $char, $n = 1)
		{
			if ($n < 0)
				return self::pos($string, $char, -$n);

			$count = mb_substr_count($string, $char);
			if ($n > $count)
				return null;

			$p = mb_strlen($string);
			for ($i = 0; $i < $n; $i++)
				$p = mb_strripos($string, $char, $p - mb_strlen($string) - 1);

			return $p;
		}
	}