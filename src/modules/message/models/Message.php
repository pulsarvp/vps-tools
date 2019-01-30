<?php

	namespace vps\tools\modules\message\models;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2019
	 * @date      2019-01-30
	 */

	/**
	 * This is the model class for table "message".
	 *
	 * @property integer       $id
	 * @property string        $language
	 * @property string        $translation
	 *
	 * @property SourceMessage $id0
	 */
	class Message extends \yii\db\ActiveRecord
	{
		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return '{{%message}}';
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'id', 'language' ], 'required' ],
				[ [ 'id' ], 'integer' ],
				[ [ 'translation' ], 'string' ],
				[ [ 'language' ], 'string', 'max' => 16 ],
				[ [ 'id' ], 'exist', 'skipOnError' => true, 'targetClass' => SourceMessage::className(), 'targetAttribute' => [ 'id' => 'id' ] ],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'language'    => 'Language',
				'translation' => 'Translation',
			];
		}
	}