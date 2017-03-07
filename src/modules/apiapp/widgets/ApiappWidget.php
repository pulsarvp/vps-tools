<?php
	namespace vps\tools\modules\apiapp\widgets;

	use vps\tools\helpers\StringHelper;
	use vps\tools\modules\apiapp\models\Apiapp;
	use Yii;
	use yii\base\Widget;
	use yii\web\View;

	/**
	 * Class ApiappWidget
	 *
	 * @package vps\tools\modules\apiapp\widgets
	 */
	class ApiappWidget extends Widget
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
								'Form' => '\vps\tools\html\Form'
							]
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
			$apiapps = Apiapp::find()->orderBy('name')->all();

			return $this->renderFile('@appViews/index.tpl', [
				'title'   => Yii::tr('Manage api application', [], 'apiapp'),
				'apiapps' => $apiapps,
				'appnew'  => $this->addApp()
			]);
		}

		/**
		 * Add new app and token
		 */
		private function addApp ()
		{
			$appNew = new Apiapp();
			if (Yii::$app->request->post('method') == 'apiapp-add')
			{
				$appNew->setAttributes(Yii::$app->request->post('Apiapp'));
				$appNew->token = StringHelper::random(16);
				if ($appNew->validate())
				{
					$appNew->save();
					$appNew->setAttributes([ 'name' => '', 'token' => '' ]);
					Yii::$app->response->redirect(Yii::$app->request->referrer . '#' . $appNew->name);
				}
			}

			return $appNew;
		}
	}