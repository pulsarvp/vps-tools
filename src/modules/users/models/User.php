<?php
	namespace vps\tools\modules\users\models;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\modules\users\interfaces\UserInterface;
	use Yii;
	use yii\db\ActiveRecord;
	use yii\web\IdentityInterface;

	/**
	 * @property string  $email
	 * @property integer $id
	 * @property integer $active
	 * @property string  $name
	 * @property string  $profile
	 * @property string  $loginDT
	 */
	class User extends ActiveRecord implements UserInterface, IdentityInterface
	{
		/**
		 * Role admin
		 */
		const R_ADMIN = 'admin';
		/**
		 * Role registered user
		 */
		const R_REGISTERED = 'registered';

		private $auth_key;

		// Getters and setters.

		/**
		 * @inheritdoc
		 */
		public function getAuthKey ()
		{
			return $this->auth_key;
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
		public function getRole ()
		{
			$auth = Yii::$app->getAuthManager();
			$roles = $auth->getRolesByUser($this->id);
			$role = array_shift($roles);

			return $role ? $role->name : null;
		}

		/**
		 * Gets user all roles.
		 *
		 * @return array
		 */
		public function getRoles ()
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
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'email'   => Yii::tr('Email', [], 'user'),
				'id'      => Yii::tr('ID', [], 'user'),
				'active'  => Yii::tr('Active', [], 'user'),
				'name'    => Yii::tr('Name', [], 'user'),
				'profile' => Yii::tr('Profile', [], 'user'),
				'loginDT' => Yii::tr('Login Dt', [], 'user'),
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
			return static::findOne([ 'access_token' => $token ]);
		}

		/**
		 * Generates auth key.
		 */
		public function generateAuthKey ()
		{
			$this->auth_key = Yii::$app->security->generateRandomString();
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
		public function register ($name, $email, $profile)
		{
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
				[ [ 'active' ], 'integer' ],
				[ [ 'name', 'email' ], 'string', 'max' => 255 ],
				[ [ 'email' ], 'unique' ],
				[ [ 'profile' ], 'string', 'max' => 45 ],
				[ [ 'loginDT' ], 'safe' ]
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
		 * @return string
		 */
		public function getName ()
		{
			return $this->name;
		}

		/**
		 * Checks the active user.
		 * @return bool.
		 */
		public function isActive ()
		{
			return $this->active;
		}
	}