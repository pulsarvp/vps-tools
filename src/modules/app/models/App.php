<?php
	namespace vps\tools\modules\app\models;

	use vps\tools\helpers\StringHelper;
	use Yii;
	use yii\db\ActiveRecord;
	use yii\web\IdentityInterface;

	/**
	 * @property integer $id
	 * @property string  $name
	 * @property string  $token
	 */
	class App extends ActiveRecord implements IdentityInterface
	{
		private $_authKey;

		/**
		 * @inheritdoc
		 */
		public function getAuthKey ()
		{
			return $this->_authKey;
		}

		/**
		 * @inheritdoc
		 */
		public function getId ()
		{
			return $this->id;
		}

		/**
		 * Finds App token by its name.
		 *
		 * @param string $name     App name.
		 * @param bool   $generate Whether to generate token if no App is found
		 *                         with given name.
		 *
		 * @return string
		 */
		public static function getTokenForName ($name, $generate = true)
		{
			$object = self::findOne([ 'name' => $name ]);
			if ($object == null)
			{
				$object = new App([
					'name'  => $name,
					'token' => StringHelper::random(16)
				]);
				$object->save();
			}

			return $object->token;
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'    => 'ID',
				'name'  => 'Name',
				'token' => 'Token',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'name' ], 'string', 'max' => 45 ],
				[ [ 'token' ], 'string', 'max' => 32 ]
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'apiapp';
		}

		/**
		 * Generate random authKey
		 */
		public function generateAuthKey ()
		{
			$this->_authKey = Yii::$app->security->generateRandomString();
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
			return static::findOne([ 'token' => $token ]);
		}

		/**
		 * @inheritdoc
		 */
		public function validateAuthKey ($authKey)
		{
			return $this->getAuthKey() === $authKey;
		}
	}

