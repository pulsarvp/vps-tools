<?php

	namespace vps\tools\modules\page\models;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	use vps\tools\db\Model;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\TimeHelper;
	use Yii;
	use yii\helpers\Inflector;

	/**
	 * This is the model class for table "page".
	 *
	 * @property integer $id
	 * @property string  $guid
	 * @property string  $title
	 * @property string  $text
	 * @property boolean $active
	 * @property string  $dt
	 *
	 */
	class Page extends Model
	{
		public $menus = [];
		public $updateUrl = false;

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMenu ()
		{
			return $this->hasMany(Yii::$app->getModule('pages')->modelMenu, [ 'id' => 'menuID' ])
				->viaTable('pagemenu', [ 'pageID' => 'id' ]);
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'page';
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'     => Yii::tr('ID', [], 'page'),
				'guid'   => Yii::tr('Guid', [], 'page'),
				'title'  => Yii::tr('Title', [], 'page'),
				'text'   => Yii::tr('Text', [], 'page'),
				'active' => Yii::tr('Active', [], 'page'),
				'dt'     => Yii::tr('Date', [], 'page'),
				'menu'   => Yii::tr('Menu', [], 'page'),
				'updateUrl'   => Yii::tr('Update selected menu items URL to match this page', [], 'page'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function beforeSave ($insert)
		{
			if (parent::beforeSave($insert))
			{
				if ($this->isNewRecord)
				{
					if (empty($this->guid))
					{
						$this->generateGuid();
					}
				}

				return true;
			}

			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'guid', 'title', 'text' ], 'trim' ],
				[ [ 'guid', 'title' ], 'string', 'max' => 128 ],
				[ [ 'title', 'text' ], 'required' ],
				[ [ 'guid' ], 'unique' ],
				[ [ 'text' ], 'string' ],
				[ [ 'active' ], 'integer' ],
				[ [ 'dt' ], 'datetime', 'format' => 'php:' . TimeHelper::$dtFormat ],
			];
		}

		private function generateGuid ()
		{
			$this->guid = Inflector::slug($this->title);
			while (!$this->validate([ 'guid' ]))
			{
				if (strlen($this->guid) > 128)
				{
					$this->guid = substr($this->guid, 0, 128);
					$this->guid = substr($this->guid, 0, strrpos($this->guid, '-'));
				}
				else
					$this->guid .= StringHelper::random(1);
			}
		}

	}