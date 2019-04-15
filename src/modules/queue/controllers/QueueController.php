<?php

	namespace vps\tools\modules\queue\controllers;

	use app\base\Controller;
	use vps\tools\modules\queue\models\Queue;
	use Yii;
	use yii\web\BadRequestHttpException;
	use yii\web\NotFoundHttpException;

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

		public function actionChangePriority ()
		{
			if (!Yii::$app->request->isAjax)
			{
				Yii::$app->end();
			}

			$queueId = Yii::$app->request->post('queueId');
			$priority = Yii::$app->request->post('priority');

			if (empty($queueId) || empty($priority))
			{
				throw new BadRequestHttpException();
			}

			$queue = Queue::findOne([ 'id' => $queueId ]);
			if ($queue === null)
			{
				throw new NotFoundHttpException();
			}

			$queue->priority = $priority;
			if (!$queue->save())
			{
				echo Json::encode([ 'error' => $queue->getFirstError('priority') ]);
				Yii::$app->end();
			}

			echo $queue->priority;
			Yii::$app->end();
		}
	}