<?php

	namespace tests\models;

	use vps\tools\base\BaseOrderModel;
	use Yii;

	/**
	 * This is the model class for table "base_test".
	 *
	 * @property integer $id
	 * @property integer $order
	 * @property string  $uuid
	 * @property string  $createDT
	 * @property string  $dt
	 * @inheritdoc
	 */
	class BaseTest extends BaseOrderModel
	{

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'       => Yii::t('app', 'ID'),
				'order'    => Yii::t('app', 'Order'),
				'uuid'     => Yii::t('app', 'Uuid'),
				'createDT' => Yii::t('app', 'CreateDT'),
				'dt'       => Yii::t('app', 'Dt'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'order' ], 'integer' ],
				[ [ 'uuid' ], 'string' ],
				[ [ 'createDT', 'dt' ], 'safe' ],
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'base_test';
		}

	}

