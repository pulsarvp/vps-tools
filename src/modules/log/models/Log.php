<?php

	namespace vps\tools\modules\log\models;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-05-24
	 */
	use vps\tools\db\Model;
	use vps\tools\helpers\TimeHelper;
	use Yii;

	/**
	 * This is the model class for table "log".
	 *
	 * @property integer $userID
	 * @property string  $email
	 * @property string  $type
	 * @property string  $action
	 * @property string  $url
	 * @property string  $server
	 * @property string  $session
	 * @property string  $cookie
	 * @property string  $post
	 * @property string  $dt
	 *
	 */
	class Log extends Model
	{
		public $menus     = [];
		public $updateUrl = true;

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(Yii::$app->getModule('logs')->modelUser, [ 'id' => 'userID' ]);
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'log';
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'userID'  => Yii::tr('UserID', [], 'log'),
				'email'   => Yii::tr('Email', [], 'log'),
				'type'    => Yii::tr('Type', [], 'log'),
				'action'  => Yii::tr('Action', [], 'log'),
				'url'     => Yii::tr('Url', [], 'log'),
				'server'  => Yii::tr('Server', [], 'log'),
				'session' => Yii::tr('Session', [], 'log'),
				'cookie'  => Yii::tr('Cookie', [], 'log'),
				'post'    => Yii::tr('Post', [], 'log'),
				'dt'      => Yii::tr('Dt', [], 'log'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'userID' ], 'integer' ],
				[ [ 'action', 'url', 'email' ], 'trim' ],
				[ [ 'email', 'action' ], 'string', 'max' => 255 ],
				[ [ 'url' ], 'string', 'max' => 1000 ],
				[ [ 'server', 'session', 'cookie', 'post', 'type' ], 'string' ],
				[ [ 'dt' ], 'datetime', 'format' => 'php:' . TimeHelper::$dtFormat ],
			];
		}

	}