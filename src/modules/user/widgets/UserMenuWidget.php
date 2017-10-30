<?php

	namespace vps\tools\modules\user\widgets;

	use vps\tools\helpers\Url;
	use Yii;
	use yii\base\Widget;
	use yii\web\View;

	class UserMenuWidget extends Widget
	{

		public $loginUrl = null;

		public $useUserLink = false;

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
					]
				]
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$auth_client_default = Yii::$app->settings->get('auth_client_default');
			if (is_null($this->loginUrl))
				if ($auth_client_default)
					$this->loginUrl = Url::toRoute([ '/user/auth', 'authclient' => $auth_client_default ]);
				else
					$this->loginUrl = Url::toRoute([ '/user/login' ]);

			return $this->renderFile('@userViews/menu.tpl', [
				'useUserLink' => $this->useUserLink,
				'loginUrl'    => $this->loginUrl,
			]);
		}

	}