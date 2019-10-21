<?php

	namespace vps\tools\modules\user\models;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\modules\user\interfaces\UserInterface;
	use Yii;
	use yii\web\IdentityInterface;

	/**
	 * @property string  $email
	 * @property integer $id
	 * @property integer $active
	 * @property string  $name
	 * @property string  $profile
	 * @property string  $loginDT
	 * @property string  $activeDT
	 * @property string  $image
	 */
	class User extends \yii\db\ActiveRecord implements UserInterface, IdentityInterface
	{
		/**
		 * Role admin
		 */
		const R_ADMIN = 'admin';
		/**
		 * Role registered user
		 */
		const R_REGISTERED = 'registered';

		private $_authkey;

		// Getters and setters.

		/**
		 * @inheritdoc
		 */
		public function getAuthKey ()
		{
			return $this->_authkey;
		}

		/**
		 * @inheritdoc
		 */
		public function getId ()
		{
			return $this->id;
		}

		/**
		 * Gets user role.
		 *
		 * @return string|null
		 */
		public function getRoleName ()
		{
			$auth = Yii::$app->getAuthManager();
			$roles = $auth->getRolesByUser($this->id);
			$role = current($roles);

			return $role ? $role->name : null;
		}

		/**
		 * Gets user all roles.
		 *
		 * @return array
		 */
		public function getRolesNames ()
		{
			$auth = Yii::$app->getAuthManager();
			$rolesByUser = $auth->getRolesByUser($this->id);
			$roles = ArrayHelper::objectsAttribute($rolesByUser, 'name');

			return $roles;
		}

		/**
		 * Assigns role to user.
		 *
		 * @param string $name the name role
		 */
		public function assignRole ($name)
		{
			$auth = Yii::$app->getAuthManager();
			$role = $auth->getRole($name);
			if ($role)
				$auth->assign($role, $this->id);
		}

		/**
		 * Assigns role to user.
		 *
		 * @param string[] $names the roles names
		 */
		public function assignRoles ($names)
		{
			foreach ($names as $name)
				$this->assignRole($name);
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			if (isset(Yii::$app->i18n->translations[ 'user' ]))
				$category = 'user';
			else
				$category = 'app';

			return [
				'email'    => Yii::tr('Email', [], $category),
				'id'       => Yii::tr('ID', [], $category),
				'active'   => Yii::tr('Active', [], $category),
				'name'     => Yii::tr('Name', [], $category),
				'profile'  => Yii::tr('Profile', [], $category),
				'loginDT'  => Yii::tr('Login Dt', [], $category),
				'activeDT' => Yii::tr('ActiveDT', [], $category),
				'image'    => Yii::tr('Image', [], $category),
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function findIdentity ($id)
		{
			return static::findOne($id);
		}

		/**
		 * @inheritdoc
		 */
		public static function findIdentityByAccessToken ($token, $type = null)
		{
			return null;
		}

		/**
		 * Generates auth key.
		 */
		public function generateAuthKey ()
		{
			$this->_authkey = Yii::$app->security->generateRandomString();
		}

		/**
		 * Saves user with given parameters.
		 *
		 * @param string $name
		 * @param string $email
		 * @param string $profile
		 *
		 * @return bool Whether user save was successful.
		 */
		public function register ($name, $email, $profile, $active)
		{
			$this->active = $active;
			$this->name = $name;
			$this->email = $email;
			$this->profile = $profile;
			$this->loginDT = TimeHelper::now();

			return $this->save();
		}

		/**
		 * Revokes all roles to user.
		 */
		public function revokeAllRoles ()
		{
			$auth = Yii::$app->getAuthManager();
			$auth->revokeAll($this->id);
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'active' ], 'boolean' ],
				[ [ 'name' ], 'string', 'length' => [ 1, 128 ] ],
				[ [ 'email' ], 'string', 'length' => [ 6, 128 ] ],
				[ [ 'email' ], 'unique' ],
				[ [ 'image' ], 'string', 'max' => 255 ],
				[ [ 'profile' ], 'string', 'max' => 45 ],
				[ [ 'loginDT', 'activeDT' ], 'safe' ]
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'user';
		}

		/**
		 * @inheritdoc
		 */
		public function validateAuthKey ($authKey)
		{
			return $this->getAuthKey() === $authKey;
		}

		/**
		 * Returns an name user.
		 *
		 * @return string
		 */
		public function getName ()
		{
			return $this->name;
		}

		/***
		 * Alias to [[hasPermission()]] to support legacy code.
		 *
		 * @deprecated
		 */
		public function isPermission ($permission = null, $and = false)
		{
			return $this->hasPermission($permission, $and);
		}

		/***
		 * Check if user has given permission.
		 *
		 * @param string|array|null $permission
		 * @param bool              $and Whether to use AND or OR operator.
		 * @return bool
		 */
		public function hasPermission ($permission = null, $and = false)
		{
			if (Yii::$app->user->can(User::R_ADMIN))
				return true;

			if ($permission == null)
				return false;

			if (is_string($permission))
			{
				return Yii::$app->user->can($permission, [], false);
			}
			elseif (is_array($permission))
			{
				foreach ($permission as $item)
				{
					if ($and)
					{
						if (!Yii::$app->user->can($item, [], false))
							return false;
					}
					else
					{
						if (Yii::$app->user->can($item, [], false))
							return true;
					}
				}
			}

			return false;
		}

		/***
		 * Checks if user has given role.
		 *
		 * @param string $role
		 * @return bool
		 */
		public function hasRole ($role)
		{
			return in_array($role, $this->getRolesNames());
		}
	}