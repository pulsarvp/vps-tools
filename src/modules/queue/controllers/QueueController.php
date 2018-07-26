<?php

	namespace vps\tools\modules\queue\controllers;

	use app\base\Controller;
	use vps\tools\modules\queue\models\Queue;
	use Yii;

	class QueueController extends Controller
	{
		public function actionIndex ()
		{
			$this->setTitle(Yii::tr('Queue management', [], 'queue'));
		}

		public function actionCancelQueue ($id)
		{
			$queue = Queue::findOne($id);
			$queue->canceled_at = time();
			$queue->save();
			$this->redirect(Yii::$app->request->referrer);
		}
	}