<?php
	namespace vps\tools\modules\rbac;

	use Yii;
	use yii\base\BootstrapInterface;

	/**
	 * Class Module
	 *
	 * @package vps\tools\modules\rbac
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\rbac\controllers';

		/**
		 * @var string the namespace that model User
		 */
		public $modelUser = 'common\models\User';

		public $title = '';

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@rbacViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'rbac/edit',
				  'route'   => $this->id . '/rbac/edit'
				],
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'rbac/user-role',
				  'route'   => $this->id . '/rbac/user-role'
				],
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'rbac/user-state',
				  'route'   => $this->id . '/rbac/user-state'
				],
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'rbac/delete-role',
				  'route'   => $this->id . '/rbac/delete-role'
				],
			], false);

			// Add module I18N category.
			if (!isset( $app->i18n->translations[ 'rbac.*' ] ))
			{
				Yii::$app->i18n->translations[ 'rbac*' ] = [
					'class'            => 'yii\i18n\PhpMessageSource',
					'basePath'         => __DIR__ . '/messages',
					'forceTranslation' => true,
					'fileMap'          => [
						'rbac' => 'rbac.php',
					]
				];
			}

			$this->title = Yii::tr('Manage user', [], 'rbac');
		}
	}