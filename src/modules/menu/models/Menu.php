<?php
	namespace vps\tools\modules\menu\models;

	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "menu".
	 *
	 * @property integer  $id
	 * @property string   $name
	 * @property string   $url
	 * @property string   $path
	 * @property integer  $lft
	 * @property integer  $rgt
	 * @property integer  $depth
	 * @property integer  $tree
	 * @property integer  $visible
	 * @property integer  $typeID
	 *
	 * @property MenuType $type
	 */
	class Menu extends ActiveRecord
	{
		public $active = false;

		public function getType ()
		{
			return $this->hasOne(MenuType::className(), [ 'id' => 'typeID' ]);
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'      => Yii::tr('ID', [], 'menu'),
				'name'    => Yii::tr('Name', [], 'menu'),
				'url'     => Yii::tr('Url', [], 'menu'),
				'path'    => Yii::tr('Path', [], 'menu'),
				'lft'     => Yii::tr('Lft', [], 'menu'),
				'rgt'     => Yii::tr('Rgt', [], 'menu'),
				'depth'   => Yii::tr('Depth', [], 'menu'),
				'visible' => Yii::tr('Visible', [], 'menu'),
				'typeID'  => Yii::tr('Type ID', [], 'menu'),
				'tree'    => Yii::tr('Tree', [], 'menu'),
			];
		}

		public function behaviors ()
		{
			return [
				'tree' => [
					'class'          => \creocoder\nestedsets\NestedSetsBehavior::className(),
					'depthAttribute' => 'depth',
					'treeAttribute'  => 'tree'
				],
			];
		}

		public static function find ()
		{
			return new \common\db\NestedSetsQuery(get_called_class());
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'lft', 'rgt', 'depth', 'visible', 'typeID', 'tree' ], 'integer' ],
				[ [ 'name', 'path','url' ], 'string', 'max' => 128 ]
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'menu';
		}

		public function transactions ()
		{
			return [
				self::SCENARIO_DEFAULT => self::OP_ALL,
			];
		}
	}