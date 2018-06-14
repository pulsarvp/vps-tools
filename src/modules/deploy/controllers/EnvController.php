<?php

	namespace vps\tools\modules\deploy\controllers;


	use app\base\Controller;
	use Yii;
	use yii\filters\AccessControl;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	class EnvController extends Controller
	{

		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow'   => true,
							'actions' => [ 'deploy' ],
							'roles'   => [ '?', '@' ]
						]
					],
				],
			];
		}

		public function actionDeploy ()
		{
			$text = Yii::$app->settings->get('app_env_deploy_text', 'Work is underway. Soon we\'ll be back.');
			$this->title = Yii::tr($text, [], 'deploy');
			$this->data('text', $text);
			$this->data('image', $this->module->img);
			$this->_tpl = '@deployViews/deploy';
		}

	}