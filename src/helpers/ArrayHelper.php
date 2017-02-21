<?php
	namespace vps\tools\helpers;

	/**
	 * Class ArrayHelper
	 * @package vps\tools\helpers
	 */
	class ArrayHelper extends \yii\helpers\BaseArrayHelper
	{
		/**
		 * Adds column to multidimensional array. First level keys of $array must be the same as $column keys.
		 * ```php
		 * $result = ArrayHelper::addColumn([1,2,3,4], [6]);
		 * ```
		 *
		 * @param  array $array
		 * @param  array $column
		 * @return array
		 */
		public static function addColumn ($array, $column)
		{
			$return = $array;
			// Check if keys are the same.
			if (count($return) == count($column) and empty( array_diff(array_keys($return), array_keys($column)) ))
			{
				foreach ($return as $k => &$ret)
					$ret[] = $column[ $k ];
			}

			return $return;
		}

		/**
		 * Unsets an element and returns its value.
		 * ```php
		 * $result = ArrayHelper::delete([1,2,3,4,5], 3);
		 * ```
		 *
		 * @param array  $array
		 * @param string $key Key name of the array element, may be specified in a dot format to retrieve the value of
		 *                    a sub-array or the property of an embedded object.
		 * @return mixed Value of the removed element.
		 */
		public static function delete (&$array, $key)
		{
			$value = null;

			if (is_array($array) and ( is_string($key) or is_numeric($key) ))
			{

				if (array_key_exists($key, $array))
				{
					$value = $array[ $key ];
					unset( $array[ $key ] );
				}

				if (( $pos = strpos($key, '.') ) !== false)
				{
					$newkey = substr($key, 0, $pos);
					if (array_key_exists($newkey, $array))
						$value = static::delete($array[ $newkey ], substr($key, $pos + 1));
				}
			}

			return $value;
		}

		/**
		 * Recursively sets all empty value in array to null.
		 * ```php
		 * $result = ArrayHelper::emptyToNull([1,2,,4]);
		 * ```
		 *
		 * @param  array $array
		 * @return array|null Exactly the input array but with null values instead of empty ones. Null if $array is not
		 *                    array.
		 */
		public static function emptyToNull ($array)
		{
			if (is_array($array))
			{
				$return = [];
				foreach ($array as $key => $item)
				{
					if (empty( $item ))
						$return[ $key ] = null;
					elseif (is_array($item))
						$return[ $key ] = self::emptyToNull($item);
					else
						$return[ $key ] = $item;
				}

				return $return;
			}

			return null;
		}

		/**
		 * Checks if all elements in array are equal.
		 * ```php
		 * if(ArrayHelper::equal([1,1,1,1]))
		 * {
		 * }
		 * ```
		 *
		 * @param array   $array
		 * @param boolean $strict Whether strict comparison should be used.
		 * @return boolean|null
		 */
		public static function equal ($array, $strict = false)
		{
			if (is_array($array))
			{
				if ($strict)
				{
					$data = array_values($array);
					for ($i = 0; $i < count($data); $i++)
					{
						for ($j = $i + 1; $j < count($data); $j++)
							if ($data[ $i ] !== $data[ $j ])
								return false;
					}

					return true;
				}
				else
					return ( count(array_unique($array)) === 1 );
			}
			else
				return null;
		}

		/**
		 * Selects from the array given keys.
		 * ```php
		 * $result = ArrayHelper::filterKeys([ 1, 3, 3, 8 ], [ 1, 3 ]);
		 * ```
		 *
		 * @param  array $array
		 * @param  array $keys
		 * @return array Values found with their corresponding keys.
		 */
		public static function filterKeys ($array, $keys)
		{
			if (is_array($array))
			{
				$return = [];
				if (!is_array($keys))
					$keys = [ $keys ];
				foreach ($keys as $key)
					if (isset( $array[ $key ] ))
						$return[ $key ] = $array[ $key ];

				return $return;
			}

			return null;
		}

		/**
		 * Flattens multidimensional array. Does not preserve key.
		 * ```php
		 * $result = ArrayHelper::flatten([ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ] ]);
		 * ```
		 *
		 * @param  array $array Array to be flattened.
		 * @return array Flattened array.
		 */
		public static function flatten ($array)
		{
			if (!is_array($array))
				return null;

			$flatten = [];
			$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
			foreach ($it as $v)
				$flatten[] = $v;

			return $flatten;
		}

		/**
		 * Checks if all keys exist in given array.
		 * ```php
		 * if(ArrayHelper::keysExist([ 1, 2, 3 ], 1))
		 * {
		 * }
		 * ```
		 *
		 * @param array $array
		 * @param array $keys
		 * @return bool|null
		 */
		public static function keysExist ($array, $keys)
		{
			if (!is_array($array))
				return null;

			if (!is_array($keys))
				$keys = [ $keys ];

			foreach ($keys as $key)
				if (!array_key_exists($key, $array))
					return false;

			return true;
		}

		/**
		 * Merges columns of the same length in multi-array.
		 * ```php
		 * $result = ArrayHelper::mergeColumns([ 1, 2, 3 ], [ 4, 5, 6 ]);
		 * ```
		 *
		 * @param  array $column1
		 * @param  array $column2
		 * @param  array $column3 ...
		 * @return array | null
		 */
		public static function mergeColumns ()
		{
			$count = [];
			$args = func_get_args();

			if (count($args) == 0)
				return [];

			foreach ($args as $arg)
			{
				if (!is_array($arg))
					return null;
				$count[] = count($arg);
			}

			if (!self::equal($count))
				return null;

			$data = [];
			$n = $count[ 0 ];
			for ($i = 0; $i < $n; $i++)
			{
				$item = [];
				foreach ($args as $arg)
					$item[] = $arg[ $i ];
				$data[] = $item;
			}

			return $data;
		}

		/**
		 * Gets random elements from array.
		 * ```php
		 * $result = ArrayHelper::mix([1,2,3,4,5],3);
		 * ```
		 *
		 * @param array   $array Input array.
		 * @param integer $num   Number of element to extract.
		 * @return array|null Array with random element from $array. Null if $array is not array.
		 */
		public static function mix ($array, $num)
		{
			if (is_array($array))
			{
				$data = [];
				$num = min($num, count($array));
				if ($num > 0)
				{
					$keys = array_rand($array, $num);
					if (!is_array($keys))
						$keys = [ $keys ];
					foreach ($keys as $key)
						$data[] = $array[ $key ];
				}

				return $data;
			}

			return null;
		}

		/**
		 * Get given attribute from array of objects.
		 * ```php
		 * $result = ArrayHelper::objectsAttribute($object, 'name');
		 * ```
		 *
		 * @param  array  $objects
		 * @param  string $attribute Attribute name.
		 * @return array
		 */
		public static function objectsAttribute ($objects, $attribute)
		{
			if (!is_array($objects))
				return null;

			$data = [];
			foreach ($objects as $object)
				$data[] = isset ( $object->$attribute ) ? $object->$attribute : null;

			return $data;
		}

		/**
		 * Recursively finds given attribute from array of objects.
		 * ```php
		 * $result = ArrayHelper::objectsAttributeRecursive($object, 'name');
		 * ```
		 *
		 * @param  array  $objects
		 * @param  string $attribute Attribute name.
		 * @param  string $children  Children attribute name.
		 * @return array
		 */
		public static function objectsAttributeRecursive ($objects, $attribute, $children = 'children')
		{
			if (!is_array($objects))
				return null;

			$data = [];
			foreach ($objects as $item)
			{
				if (isset( $item->$attribute ))
					$data[] = $item->$attribute;
				if (isset( $item->$children ))
					$data = array_merge($data, self::objectsAttributeRecursive($item->$children, $attribute, $children));
			}

			return $data;
		}

		/**
		 * Set array to multidimensional array.
		 * ```php
		 * $result = ArrayHelper::setValue($array, 'key', 'value');
		 * ```
		 *
		 * @param array  $array
		 * @param string $key   Key name of the array element, may be specified in a dot format to retrieve the value
		 *                      of a sub-array or the property of an embedded object.
		 * @param mixed  $value Value to be set.
		 */
		public static function setValue (&$array, $key, $value)
		{
			if (is_array($array) and ( is_string($key) or is_numeric($key) ))
			{
				if (( $pos = strpos($key, '.') ) === false)
				{
					$array[ $key ] = $value;
				}
				else
				{
					$newkey = substr($key, 0, $pos);
					if (!is_array($array) or !array_key_exists($newkey, $array))
						$array[ $newkey ] = [];
					static::setValue($array[ $newkey ], substr($key, $pos + 1), $value);
				}
			}
		}

		/**
		 * Checks whether given array is associative.
		 * ```php
		 * if(ArrayHelper::isAssoc(['a'=>'b']))
		 * {
		 * }
		 * ```
		 *
		 * @param  array $a
		 * @return boolean
		 */
		public static function isAssoc ($a)
		{
			foreach (array_keys($a) as $key)
				if (!is_int($key))
					return true;

			return false;
		}
	}