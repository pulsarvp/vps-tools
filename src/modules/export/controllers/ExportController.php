<?php

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 * @date      2018-06-15
	 */

	namespace vps\tools\modules\export\controllers;

	use app\base\Controller;
	use PHPExcel;
	use PHPExcel_IOFactory;
	use PHPExcel_Style_Alignment;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\Html;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\export\models\Export;
	use vps\tools\modules\log\components\LogManager;
	use Yii;
	use yii\data\ActiveDataProvider;
	use yii\data\SqlDataProvider;
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
						LogManager::info(Yii::tr('The user created the export {id}.', [ 'id' => Html::a($model->title, Url::toRoute([ 'export/view', 'id' => $model->id ])) ], 'export'));

						$this->redirect(Url::toRoute([ 'export/view', 'id' => $model->id ]));
					}
					else
						Yii::$app->notification->error(current($model->firstErrors));
				}
			}
			$this->setTitle(Yii::tr('Create export', [], 'export'));
			$this->data('model', $model);
			$this->_tpl = '@exportViews/form';
		}

		/**
		 * Delete
		 */
		public function actionDelete ($id)
		{
			if (Yii::$app->user->identity->hasPermission('deleteExport'))
			{
				$export = Export::findOne([ 'id' => $id ]);
				if ($export === null)
					Yii::$app->notification->errorToSession(Yii::tr('Given export does not exist.', [], 'export'));
				elseif ($export->delete())
				{
					Yii::$app->notification->messageToSession(Yii::tr('Export has been deleted.', [], 'export'));
					LogManager::info(Yii::tr('The user has deleted the export of "{id}".', [ 'id' => $export->title ], 'export'));
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
						LogManager::info(Yii::tr('The user changed the export of {id}.', [ 'id' => Html::a($object->title, Url::toRoute([ 'export/view', 'id' => $object->id ])) ], 'export'));

						$this->redirect(Url::toRoute([ 'export/view', 'id' => $object->id ]));
					}
					else
						Yii::$app->notification->error(current($object->firstErrors));
				}
			}
			$this->setTitle(Yii::tr('Edit export', [], 'export'));
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
			LogManager::info(Yii::tr('The user opened the list of exports.', [], 'export'));

			$this->setTitle(Yii::tr('List exports', [], 'export'));
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
			LogManager::info(Yii::tr('The user opened export {id}.', [ 'id' => Html::a($export->title, Url::toRoute([ 'export/view', 'id' => $export->id ])) ], 'export'));
			if ($export->query != '')
			{
				try
				{
					$query = trim($export->query, ';');
					$provider = new SqlDataProvider([
						'sql'        => $query,
						'pagination' => [
							'pageSize'       => 20,
							'forcePageParam' => false,
							'pageSizeParam'  => false,
							'urlManager'     => new \yii\web\UrlManager([
								'enablePrettyUrl' => true,
								'showScriptName'  => false
							])
						]
					]);

					$this->data('models', $provider->models);
					$this->data('pagination', $provider->pagination);
					$this->data('sort', $provider->sort);
				}
				catch (\Exception $e)
				{
					Yii::$app->notification->error(Yii::tr('Запрос не может быть выполнен. {error}', [ 'error' => $e->getMessage() ]));
				}
			}

			$this->setTitle($export->title);
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

			LogManager::info(Yii::tr('The user made the export {id} generation.', [ 'id' => Html::a($export->title, Url::toRoute([ 'export/view', 'id' => $export->id ])) ], 'export'));

			Yii::$app->notification->messageToSession(Yii::tr('Download <a href="{link}">file</a>.', [ 'link' => Url::toRoute([ '/' . $filename ]) ], 'export'));
			$this->redirect($_SERVER[ 'HTTP_REFERER' ]);
		}

		/**
		 * Information
		 */
		public function actionGenerateXls ($id)
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
			$filename = $dir . $export->prefix . '-' . date('Y-m-d-H-i-s') . '-' . StringHelper::random() . '.xls';

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$sheet = $objPHPExcel->getActiveSheet();

			$sheet->setTitle("Speaker");

			$str = 0;

			$cel = 0;
			$sheet->getStyle('A' . $str)->getAlignment()->setHorizontal(
				PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			foreach (range('A', 'Z') as $columnID)
			{
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
			}

			$header = array_keys($data[ 0 ]);

			$str++;
			$cel = 0;
			foreach ($header as $row1 => $td)
			{

				$sheet->setCellValueByColumnAndRow(
					$cel,
					$str,
					$td);
				$cel = $cel + 1;
			}

			if (count($data) > 0)
			{

				foreach ($data as $row => $tr)
				{
					$str++;
					$cel = 0;
					foreach ($tr as $row1 => $td)
					{
						$sheet->setCellValueByColumnAndRow(
							$cel,
							$str,
							$td);
						$cel = $cel + 1;
					}
				}
			}

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save(Yii::getAlias('@datapath') . '/' . $filename);
			unset($objWriter);
			unset($objPHPExcel);

			LogManager::info(Yii::tr('The user made the export {id} generation.', [ 'id' => Html::a($export->title, Url::toRoute([ 'export/view', 'id' => $export->id ])) ], 'export'));

			Yii::$app->notification->messageToSession(Yii::tr('Download <a href="{link}">file</a>.', [ 'link' => Url::toRoute([ '/' . $filename ]) ], 'export'));
			$this->redirect($_SERVER[ 'HTTP_REFERER' ]);
		}
	}