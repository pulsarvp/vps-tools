<?php

	namespace vps\tools\modules\queue\controllers;

	use vps\tools\controllers\WebController;
	use Yii;

	class QueueController extends WebController
	{
		public function actionIndex ()
		{
			$this->setTitle(Yii::tr('Queue management', [], 'queue'));
		}
	}