<?php
	namespace vps\tools\modules\rbac\widgets;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\modules\rbac\forms\RoleForm;
	use Yii;
	use yii\base\Widget;
	use yii\rbac\Role;
	use yii\web\View;

	class RbacWidget extends Widget
	{
		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();
			$this->view = new View([
				'renderers' => [
					'tpl' => [
						'class'   => 'yii\smarty\ViewRenderer',
						'imports' => [
							'Html' => '\vps\tools\helpers\Html',
							'Url'  => '\vps\tools\helpers\Url'
						],
						'widgets' => [
							'blocks' => [
								'Form' => '\vps\tools\html\Form',
							]
						]
					]
				]
			]);
			Yii::$app->view->registerCss('#rbac-list .value {font-family: monospace; white-space: pre-wrap}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$userClass = Yii::$app->getModule('rbac')->modelUser;

			$users = $userClass::find()->orderBy('name')->all();
			$roles = Yii::$app->authManager->getRoles();
			$data = [];
			foreach ($roles as $role)
			{
				$item = [
					'name'             => $role->name,
					'description'      => $role->description,
					'ruleName'         => $role->ruleName,
					'data'             => $role->data,
					'childRoles'       => ArrayHelper::objectsAttribute(Yii::$app->authManager->getChildren($role->name), 'name'),
					'childPermissions' => ArrayHelper::objectsAttribute(Yii::$app->authManager->getChildren($role->name), 'name'),
				];
				$data[] = $item;
			}

			$permissions = Yii::$app->authManager->getPermissions();
			$rules = Yii::$app->authManager->getRules();

			return $this->renderFile('@rbacViews/index.tpl', [
				'title'       => Yii::tr('Manage rbac', [], 'rbac'),
				'users'       => $users,
				'roles'       => $data,
				'rules'       => $rules,
				'permissions' => $permissions,
				'roleForm'    => $this->addRole()
			]);
		}

		/**
		 * Add new role
		 */
		private function addRole ()
		{
			$roleForm = new RoleForm();
			if (Yii::$app->request->getIsPost())
			{
				$roleForm->setAttributes(Yii::$app->request->post('RoleForm'));
				if ($roleForm->method == 'rbac-add')
					$roleForm->setScenario(RoleForm::SCENARIO_ADD);

				if ($roleForm->validate())
				{
					$this->saveRole($roleForm);
					$url = Yii::$app->request->referrer . '#roles';
					Yii::$app->response->redirect($url);
				}
			}

			return $roleForm;
		}

		/**
		 * Save role
		 *
		 * @param RoleForm $roleForm
		 * @param bool     $newFlag
		 */
		private function saveRole ($roleForm)
		{

			$auth = Yii::$app->getAuthManager();
			$role = $auth->createRole($roleForm->name);
			$role->type = Role::TYPE_ROLE;
			$role->description = $roleForm->description;
			if (!empty( $roleForm->ruleName ))
				$role->ruleName = $roleForm->ruleName;
			$role->data = $roleForm->data;

			if ($roleForm->method == 'rbac-add')
				$auth->add($role);
			else
			{
				$auth->update($roleForm->name, $role);
				$auth->removeChildren($role);
			}
			
			if (is_array($roleForm->childRoles) and count($roleForm->childRoles) > 0)
				foreach ($roleForm->childRoles as $childRole)
				{
					$child = $auth->getRole($childRole);
					$auth->addChild($role, $child);
				}

			if (is_array($roleForm->childPermissions) and count($roleForm->childPermissions) > 0)
				foreach ($roleForm->childPermissions as $childPermission)
				{
					$child = $auth->getPermission($childPermission);
					$auth->addChild($role, $child);
				}
		}

	}