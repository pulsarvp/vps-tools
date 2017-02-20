<?php
	namespace vps\tools\modules\setting\models;

	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * @property string $name
	 * @property string $value
	 * @property string $description
	 */
	class Setting extends ActiveRecord
	{
		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'setting';
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'name'        => Yii::tr('Name'),
				'value'       => Yii::tr('Value'),
				'description' => Yii::tr('Description'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'name' ], 'required' ],
				[ [ 'name', 'value', 'description' ], 'trim' ],
				[ [ 'name' ], 'string', 'max' => 45 ],
				[ [ 'value', 'description' ], 'string' ],
				[ [ 'name' ], 'unique' ]
			];
		}
	}