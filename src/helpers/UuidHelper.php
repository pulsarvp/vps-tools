<?php

	namespace vps\tools\helpers;

	/**
	 * Class UuidHelper
	 *
	 * @package vps\tools\helpers
	 */
	class UuidHelper
	{

		/**
		 * Generates uuid string.
		 * ```php
		 * $result = UuidHelper::generate();
		 * // $result will be:
		 * // 63e7c74a-a6e6-43e2-a644-84acd394495d
		 * ```
		 * @return string|null Generated string.
		 * @throws \Exception
		 */
		public static function generate ()
		{
			$data = random_bytes(16);

			$data[ 6 ] = chr(ord($data[ 6 ]) & 0x0f | 0x40); // set version to 0100
			$data[ 8 ] = chr(ord($data[ 8 ]) & 0x3f | 0x80); // set bits 6-7 to 10

			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}

	}