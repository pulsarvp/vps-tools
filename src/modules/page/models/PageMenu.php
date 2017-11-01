<?php

	namespace vps\tools\modules\page\models;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	use vps\tools\db\Model;
	use Yii;

	/**
	 * This is the model class for table "pagemenu".
	 *
	 * @property integer $pageID
	 * @property integer $menuID
	 *
	 * @property Page    $page
	 * @property         $menu
	 *
	 */
	class PageMenu extends Model
	{
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPage ()
		{
			return $this->hasOne(Page::className(), [ 'id' => 'pageID' ]);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMenu ()
		{
			return $this->hasOne(Yii::$app->getModule('pages')->modelMenu, [ 'id' => 'menuID' ]);
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'pagemenu';
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'pageID' => Yii::tr('Page', [], 'page'),
				'menuID' => Yii::tr('Menu', [], 'page')
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'pageID', 'menuID' ], 'integer' ],
			];
		}

	}