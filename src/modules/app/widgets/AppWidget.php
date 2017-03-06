<?php
	namespace vps\tools\modules\app\widgets;

	use vps\tools\helpers\StringHelper;
	use vps\tools\modules\app\models\App;
	use yii\base\Widget;
	use yii\web\View;
	use Yii;

	class AppWidget extends Widget
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
			$apiapps = App::find()->orderBy('name')->all();
			$appnew = new App();
			if (Yii::$app->request->post('method') == 'apiapp-add')
			{
				$appnew->setAttributes(Yii::$app->request->post('App'));
				$appnew->token = StringHelper::random(16);
				if ($appnew->validate())
				{
					$appnew->save();
					Yii::$app->response->redirect(Yii::$app->request->referrer . '#' . $appnew->name);
					$appnew->setAttributes([ 'name' => '', 'token' => '' ]);
				}
			}

			return $this->renderFile('@appViews/index.tpl', [
				'title'   => Yii::tr('Manage api application'),
				'apiapps' => $apiapps,
				'appnew'  => $appnew
			]);
		}
	}