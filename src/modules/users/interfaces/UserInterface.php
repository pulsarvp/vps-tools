<?php
	namespace vps\tools\modules\users\interfaces;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-04-19
	 */
	interface UserInterface
	{

		/**
		 * Returns an ID that can uniquely identify a user identity.
		 * @return string|int an ID that uniquely identifies a user identity.
		 */
		public function getId ();

		/**
		 * Returns an name user.
		 * @return string
		 */
		public function getName ();

		/**
		 * Gets user role.
		 *
		 * @return string|null
		 */

		public function getRole ();

		/**
		 * Gets user all roles.
		 *
		 * @return array
		 */

		public function getRoles ();

		/**
		 * Assigns role to user.
		 *
		 * @param string $name the name role
		 */
		public function assignRole ($name);

		/**
		 * Revokes all roles to user.
		 */
		public function revokeAllRoles ();

	}