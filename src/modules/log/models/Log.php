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
	 * @property string  $category
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
		public function attributeLabels ()
		{
			if (isset(Yii::$app->i18n->translations[ 'log' ]))
				$category = 'log';
			else
				$category = 'app';

			return [
				'userID'   => Yii::tr('UserID', [], $category),
				'email'    => Yii::tr('Email', [], $category),
				'type'     => Yii::tr('Type', [], $category),
				'category' => Yii::tr('Category', [], $category),
				'action'   => Yii::tr('Action', [], $category),
				'url'      => Yii::tr('Url', [], $category),
				'server'   => Yii::tr('Server', [], $category),
				'session'  => Yii::tr('Session', [], $category),
				'cookie'   => Yii::tr('Cookie', [], $category),
				'post'     => Yii::tr('Post', [], $category),
				'dt'       => Yii::tr('Dt', [], $category),
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
				[ [ 'email', 'category' ], 'string', 'max' => 255 ],
				[ [ 'url' ], 'string', 'max' => 1000 ],
				[ [ 'server', 'session', 'cookie', 'post', 'action', 'type' ], 'string' ],
				[ [ 'dt' ], 'datetime', 'format' => 'php:' . TimeHelper::$dtFormat ],
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function getDb ()
		{
			if (!empty(Yii::$app->settings->get('log_db_use')))
				return Yii::$app->logDB;
			else
				return Yii::$app->db;
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			if (!empty(Yii::$app->settings->get('log_db_use')))
				return Yii::$app->settings->get('log_db_table', 'log');
			else
				return 'log';
		}

	}