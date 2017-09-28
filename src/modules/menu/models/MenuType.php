<?php
	namespace vps\tools\modules\menu\models;

	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "menutype".
	 *
	 * @property integer $id
	 * @property string  $guid
	 * @property string  $title
	 *
	 * @property Menu[]  $menus
	 */
	class MenuType extends ActiveRecord
	{
		public function getMenus ()
		{
			return $this->hasMany(Menu::className(), [ 'typeID' => 'id' ]);
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'    => Yii::tr('ID', [], 'menu'),
				'guid'  => Yii::tr('Guid', [], 'menu'),
				'title' => Yii::tr('Title', [], 'menu'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'guid' ], 'string', 'max' => 128 ],
				[ [ 'title' ], 'string', 'max' => 255 ]
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'menutype';
		}

	}