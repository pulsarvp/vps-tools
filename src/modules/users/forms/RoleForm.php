<?php
	namespace vps\tools\modules\users\forms;

	use Yii;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-04-19
	 */
	class RoleForm extends \yii\base\Model
	{
		const SCENARIO_ADD = 'add';

		public $name;
		public $method;
		public $description;
		public $ruleName;
		public $data;
		public $childRoles;
		public $childPermissions;

		/** @inheritdoc */
		public function attributeLabels ()
		{
			return [
				'name'             => Yii::tr('Name', [], 'user'),
				'description'      => Yii::tr('Description', [], 'user'),
				'ruleName'         => Yii::tr('Rule name', [], 'user'),
				'data'             => Yii::tr('Data', [], 'user'),
				'childRoles'       => Yii::tr('Child roles', [], 'user'),
				'childPermissions' => Yii::tr('Child permissions', [], 'user'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'name' ], 'required' ],
				[ 'name', 'uniqueName', 'on' => [ self::SCENARIO_ADD ] ],
				[ [ 'name', 'method' ], 'string', 'length' => [ 1, 64 ] ],
				[ [ 'description', 'ruleName', 'data' ], 'string' ],
				[ [ 'childPermissions' ],
					'required',
					'message' => Yii::tr('Select child roles or permissions', [], 'user'),
					'when'    => function ()
					{
						if (!is_array($this->childRoles) and !is_array($this->childPermissions))
						{
							return true;
						}
					}
				],
				[ [ 'childRoles', 'childPermissions' ], 'safe' ],
			];
		}

		public function scenarios ()
		{
			$scenarios = parent::scenarios();
			$scenarios[ self::SCENARIO_ADD ] = [ 'name', 'childPermissions' ];

			return $scenarios;
		}

		public function uniqueName ($attribute)
		{
			if (!is_null(Yii::$app->authManager->getRole($this->$attribute)))
				$this->addError($attribute, Yii::tr('{attribute} "{value}" has already been taken.', [ 'attribute' => $attribute, 'value' => $this->$attribute ], 'yii'));
		}
	}