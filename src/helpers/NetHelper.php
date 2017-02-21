<?php
	namespace vps\tools\helpers;

	/**
	 * Class NetHelper
	 * @package vps\tools\helpers
	 */
	class NetHelper
	{
		/**
		 * Trying to detect apache user. If not found prompt for one.
		 * ```php
		 * $result = NetHelper::apacheUser();
		 * ```
		 * @return string|null
		 */
		public static function apacheUser ()
		{
			$possible = [ 'apache', 'www-data', '_www' ];
			foreach ($possible as $user)
			{
				if (posix_getpwnam($user) !== false)
					return $user;
			}

			$user = null;
			while (posix_getpwnam($user) === false)
				$user = readline("Enter apache username: ");

			return $user;
		}
	}