<?php

	namespace vps\tools\modules\message\controllers;

	use app\base\Controller;
	use vps\tools\helpers\Html;
	use vps\tools\helpers\Url;
	use vps\tools\modules\log\components\LogManager;
	use vps\tools\modules\message\models\SourceMessage;
	use vps\tools\modules\message\models\SourceMessageSearch;
	use Yii;
	use yii\filters\AccessControl;
	use yii\grid\GridView;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2019
	 * @date      2019-01-30
	 */
	class MessageController extends Controller
	{
		/**
		 * Lists all SourceMessage models.
		 */
		public function actionIndex ()
		{
			$searchModel = new SourceMessageSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

			$columns = [
				[ 'class' => 'yii\grid\SerialColumn' ],
				[
					'attribute' => 'category',
					'label'     => Yii::t('message', 'Category'),
					'format'    => 'text',
				],
				[
					'attribute' => 'message',
					'label'     => Yii::t('message', 'Message'),
					'format'    => 'raw',
					'value'     => function ($model) {
						return Html::a($model->message, [ '/message/view', 'id' => $model->id ]);
					},
				],
			];
			foreach (Yii::$app->getModule('messages')->languages as $one)
			{
				$columns[] = [
					'label'  => $one,
					'value'  => 'languages.' . $one,
					'filter' => '<input type="text" class="form-control" name="SourceMessageSearch[languages][' . $one . ']">',
				];
			}

			$columns[] = [ 'class'      => 'yii\grid\ActionColumn',
			               'template'   => '{update}',
			               'buttons'    => [
				               'update' => function ($url, $model) {
					               return Html::a(Html::fa('pencil'), $url, [
						               'title' => Yii::tr('Edit'),
					               ]);
				               },
			               ],
			               'urlCreator' => function ($action, $model, $key, $index) {
				               if ($action === 'update')
				               {
					               return Url::toRoute([ '/messages/message/update', 'id' => $model->id ]);
				               }
			               }
			];

			$grid = GridView::widget([
				'summary'      => '',
				'dataProvider' => $dataProvider,
				'filterModel'  => $searchModel,
				'columns'      => $columns
			]);

			$this->data('grid', $grid);

			$this->_tpl = '@messageViews/index';

			LogManager::info(Yii::tr('The user opened the list of messages.'));
		}

		/**
		 * Displays a single SourceMessage model.
		 * @param integer $id
		 */
		public function actionView ($id)
		{
			$model = $this->findModel($id);
			$this->data('model', $model);
			$this->_tpl = '@messageViews/view';

			LogManager::info(Yii::tr('The user opened message {id}.', [ 'id' => Html::a($model->message, Url::toRoute([ '/message/view', 'id' => $model->id ])) ], 'export'));
		}

		/**
		 * Creates a new SourceMessage model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 */
		public function actionCreate ()
		{
			$model = new SourceMessage();
			if ($model->load(Yii::$app->request->post()) && $model->save())
			{
				$this->redirect(Url::toRoute([ '/message/view', 'id' => $model->id ]));
			}

			$this->data('model', $model);
			$this->_tpl = '@messageViews/form';

			LogManager::info(Yii::tr('The user created the message {id}.', [ 'id' => Html::a($model->message, Url::toRoute([ '/message/view', 'id' => $model->id ])) ], 'export'));
		}

		/**
		 * Updates an existing SourceMessage model.
		 * If update is successful, the browser will be redirected to the 'view' page.
		 * @param integer $id
		 */
		public function actionUpdate ($id)
		{
			$model = $this->findModel($id);
			if ($model->load(Yii::$app->request->post()) && $model->save())
			{
				$this->redirect(Url::toRoute([ '/message/view', 'id' => $model->id ]));
			}

			$this->data('model', $model);
			$this->_tpl = '@messageViews/form';

			LogManager::info(Yii::tr('The user changed the message of {id}.', [ 'id' => Html::a($model->message, Url::toRoute([ '/message/view', 'id' => $model->id ])) ], 'export'));
		}

		/**
		 * Deletes an existing SourceMessage model.
		 * If deletion is successful, the browser will be redirected to the 'index' page.
		 * @param integer $id
		 * @return mixed
		 */
		public function actionDelete ($id)
		{
			$model = $this->findModel($id);
			$model->delete();

			LogManager::info(Yii::tr('The user has deleted the message of "{id}".', [ 'id' => Html::a($model->message, Url::toRoute([ '/message/view', 'id' => $model->id ])) ], 'export'));

			$this->redirect(Url::toRoute([ '/message/index' ]));
		}

		/**
		 * Finds the SourceMessage model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 * @param integer $id
		 * @return SourceMessage the loaded model
		 */
		private function findModel ($id)
		{
			if (( $model = SourceMessage::findOne($id) ) !== null)
			{
				return $model;
			}
			else
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given message does not exist.', [], 'message'));
				$this->redirect(Url::toRoute([ '/message/index' ]));
			}
		}
	}