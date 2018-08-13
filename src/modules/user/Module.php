<?php

	namespace vps\tools\modules\user;

	use vps\tools\helpers\ConfigurationHelper;
	use vps\tools\modules\user\models\User;
	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module user
	 *
	 * @package vps\tools\modules\user
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\user\controllers';

		/**
		 * @var string the namespace that model User
		 */
		public $modelUser = 'vps\tools\modules\user\models\User';

		/**
		 * @var boolean
		 */
		public $autoactivate = false;

		/**
		 * @var string default client oauth
		 */
		public $defaultClient = 'syncrocity';

		/**
		 * @var string default role user
		 */
		public $defaultRole = 'registered';

		/**
		 * @var int $duration number of seconds that the user can remain in logged-in status
		 */
		public $duration = 648000;

		/**
		 * @var string $duration name setting duration seconds
		 */
		public $durationSetting = null;

		/**
		 * Redirect after login
		 */
		public $redirectAfterLogin = true;
		/**
		 * Redirect after logout
		 */
		public $redirectAfterLogout = true;
		/**
		 * Use AccessControl
		 */
		public $useAccessControl = true;
		/**
		 * List of URLs not available to the guest
		 */
		public $guestRestrictedRoutes     = [];
		public $allowedUnauthorizedRoutes = [];

		public $title = '';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@userViews' => __DIR__ . '/views/user' ]);
			$app->setAliases([ '@rbacViews' => __DIR__ . '/views/rbac' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user',
				  'route'   => $this->id . '/user/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/index',
				  'route'   => $this->id . '/user/index'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/view',
				  'route'   => $this->id . '/user/view'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/delete',
				  'route'   => $this->id . '/user/delete'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/manage',
				  'route'   => $this->id . '/user/manage'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/login',
				  'route'   => $this->id . '/user/login'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/logout',
				  'route'   => $this->id . '/user/logout'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'user/auth',
				  'route'   => $this->id . '/user/auth'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'rbac/edit',
				  'route'   => $this->id . '/rbac/edit'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'rbac/user-role',
				  'verb'    => 'POST',
				  'ajax'    => true,
				  'route'   => $this->id . '/rbac/user-role'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'rbac/user-state',
				  'verb'    => 'POST',
				  'ajax'    => true,
				  'route'   => $this->id . '/rbac/user-state'
				],
				[ 'class'   => 'vps\tools\web\UrlRule',
				  'pattern' => 'rbac/delete-role',
				  'route'   => $this->id . '/rbac/delete-role'
				],
			], true);

			ConfigurationHelper::addTranslation('user', [ 'user' => 'user.php' ], __DIR__ . '/messages');

			Yii::$app->view->registerCss('.nav-user-image img { border-radius: 50%; max-height: 40px } .tools-user-image {margin: 15px; max-width: 100%}');

			$this->title = Yii::tr('User manage', [], 'user');
		}

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();
			if (empty($this->defaultRole))
				$this->defaultRole = User::R_REGISTERED;
		}
	}