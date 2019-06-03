<?php

	namespace vps\tools\modules\apiapp\widgets;

	use vps\tools\helpers\Html;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\apiapp\models\Apiapp;
	use vps\tools\modules\log\components\LogManager;
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
			$this->deleteApi();
			$appnew = $this->addApp();
			$viewID = Yii::$app->session->get('viewID', 0);
			Yii::$app->session->set('viewID', 0);

			LogManager::info(Yii::tr('User {user} has opened API applications list.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])) ]));

			return $this->renderFile('@appViews/index.tpl', [
				'title'   => Yii::tr('Manage api application', [], 'apiapp'),
				'apiapps' => $apiapps,
				'view'    => $viewID,
				'appnew'  => $appnew
			]);
		}

		/**
		 * Add new app and token
		 */
		private function addApp ()
		{
			$appNew = new Apiapp();
			if (Yii::$app->request->post('method') == 'apiapp')
			{
				$post = Yii::$app->request->post();

				if ($post[ 'id' ])
					$appNew = Apiapp::findOne($post[ 'id' ]);

				$appNew->setAttributes([ 'name' => $post[ 'name' ] ]);
				$appNew->token = StringHelper::random(16);
				if ($appNew->validate())
				{
					$appNew->save();
					if (!$post[ 'id' ])
						Yii::$app->session->set('viewID', $appNew->id);
					$url = Yii::$app->request->referrer . '#' . $appNew->name;
					$appNew->setAttributes([ 'name' => '', 'token' => '' ]);
					LogManager::info(Yii::tr('User {user} has created API application {name}.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])), 'name' => $post[ 'name' ] ]));

					Yii::$app->response->redirect($url);
					Yii::$app->end();
				}
			}

			return $appNew;
		}

		/**
		 * Удаление приложения API
		 */
		private function deleteApi ()
		{
			if (Yii::$app->request->get('action', false) === 'delete')
			{
				$model = Apiapp::findOne(Yii::$app->request->get('id', 0));
				if(!empty($model)){
					$model->delete();
				}

				Yii::$app->controller->redirect(['appapi/index']);
				Yii::$app->end();
			}
		}
	}