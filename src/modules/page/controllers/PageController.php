<?php

	namespace vps\tools\modules\page\controllers;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\HumanHelper;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\Url;
	use vps\tools\modules\page\models\Page;
	use vps\tools\modules\page\models\PageMenu;
	use Yii;
	use yii\filters\AccessControl;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */
	class PageController extends \app\base\Controller
	{
		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow'   => true,
							'actions' => [ 'frontend' ],
							'roles'   => [ '?', '@' ]
						],
						[
							'allow'         => true,
							'actions'       => [ 'image', 'view', 'add', 'edit', 'activate', 'delete' ],
							'roles'         => [ '@' ],
							'matchCallback' => function ($rule, $action)
							{
								if (!Yii::$app->user->identity->active or !(Yii::$app->user->can('admin') or Yii::$app->user->can('admin_page')))
								{
									Yii::$app->notification->errorToSession(Yii::tr('You have no permissions.', [], 'user'));
									$this->redirect(Url::toRoute([ '/site/index' ]));

									return false;
								}
								else
									return true;
							}
						]
					],
				],
			];
		}

		/*
		 * This action is for AJAX request.  It is updated page active
		 */
		public function actionActivate ()
		{
			if (Yii::$app->request->isAjax)
			{
				$post = Yii::$app->request->post();
				$page = Page::findOne($post[ 'id' ]);
				$page->updateAttributes([ 'active' => $page->active ? 0 : 1 ]);

				echo $page->active;
			}
			Yii::$app->end();
		}

		public function actionAdd ()
		{
			$this->_menulist();
			$model = new Page;
			$this->savePage($model);

			$this->setTitle(Yii::tr('Add page', [], 'page'));
			$this->data('model', $model);
			$this->_tpl = '@pageViews/form';
		}

		/**
		 * Delete page
		 *
		 * @param $id page
		 */
		public function actionDelete ($id)
		{
			$page = Page::findOne([ 'id' => $id ]);
			if ($page === null)
				Yii::$app->notification->errorToSession(Yii::tr('Given page does not exist.', [], 'page'));

			if ($page->delete())
				Yii::$app->notification->messageToSession(Yii::tr('Page has been deleted.', [], 'page'));
			else
				Yii::$app->notification->errorToSession(Yii::tr('Page has not been deleted.', [], 'page'));
			$this->redirect(Url::toRoute([ '/page/index' ]));
		}

		public function actionEdit ($id)
		{
			$this->_menulist();
			$model = Page::find()->where([ 'id' => $id ])->orWhere([ 'guid' => $id ])->one();
			if ($model === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given page does not exist.', [], 'page'));
				$this->redirect(Url::toRoute([ '/page/index' ]));
			}
			if ($this->module->useMenu)
				$model->menus = ArrayHelper::objectsAttribute($model->menu, 'id');

			$this->savePage($model);
			$this->setTitle(Yii::tr('Edit page', [], 'page'));
			$this->data('model', $model);
			$this->_tpl = '@pageViews/form';
		}

		/**
		 * View frontend page
		 *
		 * @param $id page
		 */
		public function actionFrontend ($id)
		{
			$page = Page::find()->where([ 'guid' => $id ])->one();

			if ($page === null)
			{
				throw new \yii\web\HttpException(404);
			}
			$this->setTitle($page->title);
			$this->data('page', $page);
			$this->_tpl = '@pageViews/page';
		}

		public function actionImage ()
		{

			$datapath = Yii::$app->settings->get('datapath');
			$name = StringHelper::random();
			$filepath = '/img/upload/' . $name[ 0 ] . '/' . $name[ 1 ];
			$path = $datapath . $filepath;

			FileHelper::createDirectory($path);
			if (!is_writable($path))
			{
				echo json_encode([ 'error' => true, 'message' => Yii::tr('Path {path} is not writable.', [ 'path' => $path ], 'page') ]);
				Yii::$app->end();
			}

			$file = $_FILES[ 'file' ];
			$allowed = [ 'image/png', 'image/jpg', 'image/jpeg' ];
			if (!in_array(strtolower($file[ 'type' ]), $allowed))
			{
				echo json_encode([ 'error' => true, 'message' => Yii::tr('Image type {type} is not allowed.', [ 'type' => $file[ 'type' ] ], 'page') ]);
				Yii::$app->end();
			}

			if ($file[ 'size' ] > HumanHelper::maxBytesUpload())
			{
				echo json_encode([ 'error' => true, 'message' => Yii::tr('Image size exceeds {max}.', [ 'max' => HumanHelper::maxUpload() ], 'page') ]);
				Yii::$app->end();
			}

			$ext = pathinfo($file[ 'name' ], PATHINFO_EXTENSION);
			$filename = $name . '.' . $ext;

			if (file_exists($path . '/' . $filename))
				unlink($path . '/' . $filename);

			copy($file[ 'tmp_name' ], $path . '/' . $filename);
			unlink($file[ 'tmp_name' ]);

			$array = [
				'url' => $filepath . '/' . $filename,
				'id'  => 'fancybox'
			];

			echo stripslashes(json_encode($array));
			Yii::$app->end();
		}

		/**
		 * Information page
		 *
		 * @param $id page
		 */
		public function actionView ($id)
		{
			$page = Page::find()->where([ 'id' => $id ])->orWhere([ 'guid' => $id ])->one();
			if ($page === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given page does not exist.', [], 'page'));
				$this->redirect(Url::toRoute([ '/page/index' ]));
			}
			$this->setTitle(Yii::tr('View page', [], 'page'));
			$this->data('page', $page);
			$this->data('useMenu', $this->module->useMenu);
			$this->_tpl = '@pageViews/view';
		}

		private function _menulist ()
		{
			if ($this->module->useMenu)
			{
				// Get menus to display in dropdown.
				$types = $this->module->modelMenuType::find()->asArray()->all();
				$menutypes = ArrayHelper::map($types, 'id', 'title');

				$menus = $this->module->modelMenu::find()->where([ '>', 'depth', 0 ])->orderBy([ 'typeID' => SORT_ASC, 'lft' => SORT_ASC ])->all();

				$menulist = [];
				foreach ($menus as $menu)
				{
					$menulist[ $menutypes[ $menu->typeID ] ][ $menu->id ] = str_repeat('-', $menu->depth - 1) . ' ' . Yii::tr($menu->title);
				}
				$this->data('menudrop', $menulist);
			}
		}

		private function savePage ($model)
		{
			$r = Yii::$app->request;
			if ($r->isPost and $model->load($r->post()))
			{
				if ($model->save())
				{
					$page = $r->post("Page", []);

					if (isset($page[ 'menus' ]))
					{
						PageMenu::deleteAll([ 'pageID' => $model->id ]);
						if (!empty($page[ 'menus' ]))
							foreach ($page[ 'menus' ] as $menu)
							{
								$pagemenu = new PageMenu();
								$pagemenu->pageID = $model->id;
								$pagemenu->menuID = $menu;
								$pagemenu->save();
								if (isset($page[ 'updateUrl' ]))
								{
									$pagemenu->menu->url = '/page/' . $model->guid;
									$pagemenu->menu->save();
								}
							}
					}

					$this->redirect(Url::toRoute([ '/pages/page/view', 'id' => $model->id ]));
				}
				else
				{
					Yii::$app->notification->error(Yii::tr('Page has not been created.', [], 'page'));
				}
			}
		}
	}