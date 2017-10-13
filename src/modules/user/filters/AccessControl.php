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
			}
			parent::init();
		}

	}