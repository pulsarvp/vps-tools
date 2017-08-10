<?php
	namespace vps\tools\modules\queue\models;

	use yii\db\ActiveRecord;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-08-10
	 */
	class Queue extends ActiveRecord
	{

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'job'         => 'Job',
				'ttr'         => 'Ttr',
				'delay'       => 'Delay',
				'priority'    => 'Priority',
				'pushed_at'   => 'Pushed_at',
				'reserved_at' => 'Reserved_at',
				'done_at'     => 'Done_at',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'id' ], 'integer' ],
				[ [ 'job' ], 'string' ],
				[ [ 'ttr', 'delay', 'priority', 'pushed_at', 'reserved_at', 'done_at', ], 'integer' ],

			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'queue';
		}

	}
