<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 * @date      2018-06-27
	 */

	namespace vps\tools\modules\export\models;

	use vps\tools\helpers\TimeHelper;
	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * @inheritdoc
	 *
	 * @property integer $id
	 * @property string  $title
	 * @property string  $description
	 * @property string  $query
	 * @property string  $prefix
	 * @property string  $createDT
	 * @property string  $dt
	 */
	class Export extends ActiveRecord
	{

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'dt'          => Yii::tr('Dt'),
				'createDT'    => Yii::tr('CreateDT'),
				'id'          => Yii::tr('ID'),
				'title'       => Yii::tr('Title'),
				'description' => Yii::tr('Description'),
				'query'       => Yii::tr('Query'),
				'prefix'      => Yii::tr('Prefix'),
			];
		}

		public function beforeSave ($insert)
		{
			if ($parent = parent::beforeSave($insert))
			{
				if ($this->isNewRecord)
				{
					if ($this->hasAttribute('createDT'))
						$this->createDT = TimeHelper::now();
				}
				else
				{
					if ($this->hasAttribute('dt'))
						$this->dt = TimeHelper::now();
				}
			}

			return $parent;
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'title' ], 'filter', 'filter' => 'strip_tags' ],
				[ [ 'description', 'query' ], 'string' ],
				[ [ 'title', 'query', 'prefix' ], 'trim' ],
				[ [ 'title', 'query', 'prefix' ], 'required' ],
				[ [ 'title', 'prefix' ], 'string', 'length' => [ 1, 255 ] ],
				[ [ 'createDT', 'dt' ], 'safe' ],
				[ [ 'query' ], 'validateQuery' ]
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'export';
		}

		public function validateQuery ($attribute)
		{
			if (!$this->hasErrors())
			{
				$query = strtolower(trim($this->$attribute));
				preg_match("/(^| |;|`)(create|drop|update|delete|insert|truncate)( |$|;|`)/", $query, $match);
				if (count($match) > 0)
					$this->addError($attribute, Yii::tr('In the request, the forbidden commands.'));
			}
		}

	}