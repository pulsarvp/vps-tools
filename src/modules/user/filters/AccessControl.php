<?php

	namespace vps\tools\modules\user\filters;

	use vps\tools\helpers\Url;
	use Yii;
	use yii\base\Action;
	use yii\filters\AccessControl as Base;
	use yii\filters\AccessRule;
	use yii\web\ForbiddenHttpException;
	use yii\web\User;

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
			$access = ( !is_null(Yii::$app->getModule('users')) and Yii::$app->getModule('users')->useAccessControl );
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
					'roles'        => [ '?' ],
					'denyCallback' => function ($rule, $action) { Yii::$app->notification->error(Yii::tr('You are already logged in.')); },
				],
				[
					'allow'         => true,
					'actions'       => [],
					'controllers'   => [],
					'roles'         => [ '@' ],
					'matchCallback' => function ($rule, $action)
					{

						if (!Yii::$app->user->identity->active)
						{
							Yii::$app->notification->errorToSession(Yii::tr('Your account is not approved yet.', [], 'user'));
							$action->controller->redirect(Url::toRoute([ '/site/index' ]));
						}

						return true;
					}
				],
			];

			if ($access)
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

			if ($access or count($this->rules) == 0)
				foreach ($rules as $rule)
				{
					if (is_array($rule))
					{
						array_push($this->rules, Yii::createObject(array_merge($this->ruleConfig, $rule)));
					}
				}
			parent::init();
		}

		/**
		 * This method is invoked right before an action is to be executed (after all possible filters.)
		 * You may override this method to do last-minute preparation for the action.
		 *
		 * @param Action $action the action to be executed.
		 *
		 * @return bool whether the action should continue to be executed.
		 */
		public function beforeAction ($action)
		{
			$user = $this->user;

			$request = Yii::$app->getRequest();
			/* @var $rule AccessRule */
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

		/**
		 * Denies the access of the user.
		 * The default implementation will redirect the user to the login page if he is a guest;
		 * if the user is already logged, a 403 HTTP exception will be thrown.
		 *
		 * @param User|false $user the current user or boolean `false` in case of detached User component
		 *
		 * @throws ForbiddenHttpException if the user is already logged in or in case of detached User component.
		 */
		protected function denyAccess ($user)
		{

			if ($user !== false && $user->getIsGuest())
			{

				$user->loginRequired();
			}
			else
			{
				throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
			}
		}
	}