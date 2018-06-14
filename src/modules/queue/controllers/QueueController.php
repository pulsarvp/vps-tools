<?php

	namespace vps\tools\modules\queue\controllers;

	use app\base\Controller;
	use Yii;

	class QueueController extends Controller
	{
		public function actionIndex ()
		{
			$this->setTitle(Yii::tr('Queue management', [], 'queue'));
		}
	}