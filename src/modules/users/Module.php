<?php
	namespace vps\tools\modules\users;

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
		public $controllerNamespace = 'vps\tools\modules\users\controllers';

		/**
		 * @var string the namespace that model User
		 */
		public $modelUser = 'vps\tools\modules\users\models\User';

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
			], false);

			// Add module I18N category.
			if (!isset( $app->i18n->translations[ 'user.*' ] ))
			{
				Yii::$app->i18n->translations[ 'user*' ] = [
					'class'            => 'yii\i18n\PhpMessageSource',
					'basePath'         => __DIR__ . '/messages',
					'forceTranslation' => true,
					'fileMap'          => [
						'user' => 'user.php',
					]
				];
			}

			$this->title = Yii::tr('User management', [], 'user');
		}
	}