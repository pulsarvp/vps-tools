<?php
	namespace vps\tools\modules\rbac\forms;

	use Yii;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      19.04.17
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
				'name'       => Yii::tr('Name', [], 'rbac'),
				'description'       => Yii::tr('Description', [], 'rbac'),
				'ruleName' => Yii::tr('Rule name', [], 'rbac'),
				'data' => Yii::tr('Data', [], 'rbac'),
				'childRoles' => Yii::tr('Child roles', [], 'rbac'),
				'childPermissions' => Yii::tr('Child permissions', [], 'rbac'),
			];
		}


		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'name' ], 'required' ],
				/*[ 'name', 'unique', 'on' => [ self::SCENARIO_ADD ], 'when' => function ()
				{
					return ( !is_null(Yii::$app->authManager->getRole($this->name)) );
				}
				],*/
				[ [ 'name', 'method' ], 'string', 'min' => 1, 'max' => 64 ],
				[ [ 'description', 'ruleName', 'data' ], 'string' ],
				[ [ 'childPermissions' ],
					'required',
					'message' => Yii::tr('Select a child roles or premissions', [], 'rbac'),
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
			$scenarios[ self::SCENARIO_ADD ] = [ 'name', 'childRoles' ];

			return $scenarios;
		}
	}