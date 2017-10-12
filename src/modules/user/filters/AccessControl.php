<?php

	namespace vps\tools\modules\user\filters;

	use Yii;
	use yii\filters\AccessControl as Base;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-12
	 */
	class AccessControl extends Base
	{

		/**
		 * Initializes the [[rules]] array by instantiating rule objects from configurations.
		 */
		public function init ()
		{
			parent::init();
			if (Yii::$app->getModule('users')->useAccessControl)
			{
				$rules = [
					[
						'allow'       => true,
						'actions'     => [ 'index' ],
						'controllers' => [ 'site' ],
						'roles'       => [ '?', '@' ]
					],
					[
						'allow'        => true,
						'actions'      => [ 'auth', 'login' ],
						'controllers'  => [ 'user' ],
						'roles'        => [ '?', '@' ],
						'denyCallback' => function ($rule, $action) { Yii::$app->notification->error(Yii::tr('You are already logged in.')); },
					]
				];

				foreach (Yii::$app->getModule('users')->allowedUnauthorizedRoutes as $url)
				{
					$url = explode('/', trim($url, '/'));
					array_push($rules, [
						'allow'       => true,
						'actions'     => [ array_pop($url) ],
						'controllers' => [ array_shift($url) ],
						'roles'       => [ '?', '@' ]
					]);
				}

				foreach ($rules as $rule)
				{
					if (is_array($rule))
					{
						array_push($this->rules, Yii::createObject(array_merge($this->ruleConfig, $rule)));
					}
				}
				//Yii::p($rules);
				//Yii::p($this->rules);
				//die();
			}
		}

		public function beforeAction ($action)
		{
			$user = $this->user;
			$request = Yii::$app->getRequest();
			foreach ($this->rules as $rule)
			{
				if ($allow = $rule->allows($action, $user, $request))
				{
					return true;
				}
				elseif ($allow === false)
				{
					if (isset($rule->denyCallback))
					{
						call_user_func($rule->denyCallback, $rule, $action);
					}
					elseif ($this->denyCallback !== null)
					{
						call_user_func($this->denyCallback, $rule, $action);
					}
					else
					{
						$this->denyAccess($user);
					}

					return false;
				}
			}
			if ($this->denyCallback !== null)
			{
				call_user_func($this->denyCallback, null, $action);
			}
			else
			{
				$this->denyAccess($user);
			}

			return false;
		}

	}