<?php

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 * @date      2018-06-15
	 */

	namespace vps\tools\modules\export\controllers;

	use app\base\Controller;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\Html;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\export\models\Export;
	use vps\tools\modules\log\components\LogManager;
	use Yii;
	use yii\data\ActiveDataProvider;
	use yii\db\Exception;

	/**
	 * Export Controller.
	 */
	class ExportController extends Controller
	{

		/**
		 * Create page
		 */
		public function actionCreate ()
		{
			$r = Yii::$app->request;
			$post = $r->post();

			$model = new Export();

			if ($r->isPost and $model->load($post))
			{
				if ($model->validate())
				{
					if ($model->save())
					{
						LogManager::info(Yii::tr('The user {user} created the export {id}.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])), 'id' => Html::a($model->title, Url::toRoute([ 'export/view', 'id' => $model->id ])) ], 'export'));

						$this->redirect(Url::toRoute([ 'export/view', 'id' => $model->id ]));
					}
					else
						Yii::$app->notification->error(current($model->firstErrors));
				}
			}
			$this->data('model', $model);
			$this->_tpl = '@exportViews/form';
		}

		/**
		 * Delete
		 */
		public function actionDelete ($id)
		{
			if (Yii::$app->user->identity->isPermission('deleteExport'))
			{
				$export = Export::findOne([ 'id' => $id ]);
				if ($export === null)
					Yii::$app->notification->errorToSession(Yii::tr('Given export does not exist.', [], 'export'));
				elseif ($export->delete())
				{
					Yii::$app->notification->messageToSession(Yii::tr('Export has been deleted.', [], 'export'));
					LogManager::info(Yii::tr('The user {user} has deleted the export of "{id}".', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])), 'id' => $export->title ], 'export'));
				}
				else
					Yii::$app->notification->errorToSession(Yii::tr('Export has not been deleted.', [], 'export'));
				$this->redirect(Url::toRoute([ 'export/index' ]));
			}
		}

		/**
		 * Edit page
		 */
		public function actionEdit ($id)
		{
			$r = Yii::$app->request;
			$post = $r->post();

			$object = Export::findOne($id);
			if ($object === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given export does not exist.', [], 'export'));
				$this->redirect(Url::toRoute([ 'export/index' ]));
			}

			if ($r->isPost and $object->load($post))
			{
				if ($object->validate())
				{
					if ($object->save())
					{
						LogManager::info(Yii::tr('The user {user} changed the export of {id}.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])), 'id' => Html::a($object->title, Url::toRoute([ 'export/view', 'id' => $object->id ])) ], 'export'));

						$this->redirect(Url::toRoute([ 'export/view', 'id' => $object->id ]));
					}
					else
						Yii::$app->notification->error(current($object->firstErrors));
				}
			}
			$this->data('model', $object);
			$this->_tpl = '@exportViews/form';
		}

		/*
		 * List
		 */
		public function actionIndex ()
		{
			$query = Export::find();

			$provider = new ActiveDataProvider([
				'query'      => $query,
				'sort'       => [
					'attributes'   => [
						'id',
						'title',
						'prefix',
						'createDT',
						'dt'
					],
					'defaultOrder' => [
						'id' => SORT_ASC
					]
				],
				'pagination' => [
					'pageSize'       => Yii::$app->settings->get('page_size_object', 10),
					'forcePageParam' => false,
					'pageSizeParam'  => false,
					'urlManager'     => new \yii\web\UrlManager([
						'enablePrettyUrl' => true,
						'showScriptName'  => false,
						'rules'           => [
							'export/page/<page>' => 'export/index'
						]
					])
				]
			]);
			LogManager::info(Yii::tr('The user {user} opened the list of exports.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])) ], 'export'));

			$this->data('models', $provider->models);
			$this->data('pagination', $provider->pagination);
			$this->data('sort', $provider->sort);
			$this->_tpl = '@exportViews/index';
		}

		/**
		 * Information
		 */
		public function actionView ($id)
		{
			$export = Export::find()->where([ 'id' => $id ])->one();
			if ($export === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given export does not exist.', [], 'export'));
				$this->redirect(Url::toRoute([ 'export/index' ]));
			}
			LogManager::info(Yii::tr('The user {user} opened export {id}.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'view/view', 'id' => Yii::$app->user->id ])), 'id' => Html::a($export->title, Url::toRoute([ 'export/view', 'id' => $export->id ])) ], 'export'));

			$this->data('export', $export);
			$this->_tpl = '@exportViews/view';
		}

		/**
		 * Information
		 */
		public function actionGenerate ($id)
		{
			$export = Export::find()->where([ 'id' => $id ])->one();
			if ($export === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given export does not exist.', [], 'export'));
				$this->redirect(Url::toRoute([ 'export/index' ]));
			}
			$data = [];
			try
			{
				$data = Yii::$app->db->createCommand($export->query)->queryAll();
			}
			catch (Exception $e)
			{
				Yii::$app->notification->errorToSession($e->getMessage());
				$this->redirect(Url::toRoute([ 'export/index' ]));
			}
			$dir = 'file/export/';

			FileHelper::createDirectory(Yii::getAlias('@datapath') . '/' . $dir);
			$filename = $dir . $export->prefix . '-' . date('Y-m-d-H-i-s') . '-' . StringHelper::random() . '.csv';

			$fp = fopen($filename, 'w');
			fputcsv($fp, array_keys($data[ 0 ]));
			foreach ($data as $fields)
			{
				fputcsv($fp, $fields);
			}

			fclose($fp);

			LogManager::info(Yii::tr('The user {user} made the export {id} generation.', [ 'user' => Html::a(Yii::$app->user->identity->name, Url::toRoute([ 'user/view', 'id' => Yii::$app->user->id ])), 'id' => Html::a($export->title, Url::toRoute([ 'export/view', 'id' => $export->id ])) ], 'export'));

			Yii::$app->notification->messageToSession(Yii::tr('Download <a href="{link}">file</a>.', [ 'link' => Url::toRoute([ '/' . $filename ]) ], 'export'));
			$this->redirect($_SERVER[ 'HTTP_REFERER' ]);
		}
	}