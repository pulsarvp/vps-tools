<?php

	namespace vps\tools\dictionaries;

	/**
	 * Class Base dictionaries
	 *
	 * @package vps\tools\helpers
	 */

	abstract class Base
	{
		public static function exists ($name)
		{
			$reflection = new \ReflectionClass(get_called_class());
			$value = $reflection->getConstant(strtoupper($name));

			return $value !== false;
		}

		public static function all ()
		{
			$reflection = new \ReflectionClass(get_called_class());

			return array_values($reflection->getConstants());
		}
	}
