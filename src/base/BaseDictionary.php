<?php

	namespace vps\tools\base;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 */

	abstract class BaseDictionary
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
