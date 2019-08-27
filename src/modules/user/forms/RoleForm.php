<?php

	namespace vps\tools\modules\user\forms;

	use Yii;
	use vps\tools\helpers\ArrayHelper;
	use yii\rbac\Role;

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
				[ [ 'name', 'method' ], 'string', 'length' => [ 1, 64 ] ],
				[ 'name', 'uniqueName', 'on' => [ self::SCENARIO_ADD ] ],
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

		/**
		 * Загрузка свойств по имени
		 * @param $name
		 */
		public function loadByName ($name)
		{
			$role = Yii::$app->authManager->getRole($name);
			if ($role === null)
			{
				return;
			}

			$this->name = $role->name;
			$this->description = $role->description;
			$this->ruleName = $role->ruleName;
			$this->data = $role->data;
			$this->childRoles = ArrayHelper::objectsAttribute(Yii::$app->authManager->getChildren($role->name), 'name');
			$this->childPermissions = ArrayHelper::objectsAttribute(Yii::$app->authManager->getChildren($role->name), 'name');
		}

		/**
		 * Сохранение
		 */
		public function save ()
		{
			$auth = Yii::$app->getAuthManager();
			$role = $auth->createRole($this->name);
			$role->type = Role::TYPE_ROLE;
			$role->description = $this->description;
			if (!empty($this->ruleName))
			{
				$role->ruleName = $this->ruleName;
			}

			$role->data = $this->data;

			if ($this->method == 'rbac-add')
			{
				$auth->add($role);
			}
			else
			{
				$auth->update($this->name, $role);
				$auth->removeChildren($role);
			}

			if (is_array($this->childRoles) and count($this->childRoles) > 0)
				foreach ($this->childRoles as $childRole)
				{
					$child = $auth->getRole($childRole);
					$auth->addChild($role, $child);
				}

			if (is_array($this->childPermissions) and count($this->childPermissions) > 0)
				foreach ($this->childPermissions as $childPermission)
				{
					$child = $auth->getPermission($childPermission);
					$auth->addChild($role, $child);
				}
		}
	}