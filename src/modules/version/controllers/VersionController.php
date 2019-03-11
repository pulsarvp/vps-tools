<?php

	namespace vps\tools\modules\version\controllers;

	use Yii;
	use yii\base\Controller;
	use yii\web\Response;

	/**
	 * Class VersionController
	 *
	 * @package vps\tools\modules\version\controllers
	 */
	class VersionController extends Controller
	{
		public function actionIndex ()
		{
			Yii::$app->response->format = Response::FORMAT_RAW;
			Yii::$app->response->headers->add('Content-Type', 'text/plain');

			return Yii::$app->version;
		}
	}