<?php

	namespace vps\tools\modules\page\controllers;

	use vps\tools\helpers\ArrayHelper;
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
							'allow'   => true,
							'actions' => [ 'view', 'add', 'edit', 'activate', 'delete' ],
							'roles'   => [ '@' ]
						],
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
				$active = $page->active ? 0 : 1;
				$page->updateAttributes([ 'active' => $active ]);

				echo $active;
			}
			Yii::$app->end();
		}

		public function actionAdd ()
		{
			$r = Yii::$app->request;

			$this->_menulist();
			$model = new Page;
			if ($r->isPost and $model->load($r->post()))
			{
				if ($model->validate())
				{
					if ($model->save())
					{
						$page = $r->post("Page", []);

						if (isset($page[ 'menus' ]))
						{
							foreach ($page[ 'menus' ] as $menu)
							{
								$pagemenu = new PageMenu();
								$pagemenu->pageID = $model->id;
								$pagemenu->menuID = $menu;
								$pagemenu->save();
							}
						}
						$this->redirect(Url::toRoute([ '/pages/page/view', 'id' => $model->id ]));
					}
					else
						Yii::$app->notification->errorToSession(Yii::tr('Page has not been created.', [], 'page'));
				}
			}

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
			$r = Yii::$app->request;
			$this->_menulist();
			$model = Page::find()->where([ 'id' => $id ])->orWhere([ 'guid' => $id ])->one();
			if ($model === null)
			{
				Yii::$app->notification->errorToSession(Yii::tr('Given page does not exist.', [], 'page'));
				$this->redirect(Url::toRoute([ '/page/index' ]));
			}
			if ($this->module->useMenu)
				$model->menus = ArrayHelper::objectsAttribute($model->menu, 'id');
			if ($r->isPost and $model->load($r->post()))
			{
				if ($model->validate())
				{
					if ($model->save())
					{
						$page = $r->post("Page", []);

						if (isset($page[ 'menus' ]))
						{
							PageMenu::deleteAll([ 'pageID' => $model->id ]);

							foreach ($page[ 'menus' ] as $menu)
							{
								$pagemenu = new PageMenu();
								$pagemenu->pageID = $model->id;
								$pagemenu->menuID = $menu;
								$pagemenu->save();
							}
						}

						$this->redirect(Url::toRoute([ '/pages/page/view', 'id' => $model->id ]));
					}
					else
						Yii::$app->notification->errorToSession(Yii::tr('Page has not been created.', [], 'page'));
				}
			}

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
			$page = Page::find()->where([ 'id' => $id ])->orWhere([ 'guid' => $id ])->one();

			if ($page === null)
			{
				Yii::$app->notification->error(Yii::tr('Given page does not exist.', [], 'page'));
			}

			$this->data('page', $page);
			$this->_tpl = '@pageViews/page';
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

				$menus = $this->module->modelMenu::find()->where('level>0')->orderBy([ 'typeID' => SORT_ASC, 'lft' => SORT_ASC ])->all();
				$menulist = [];
				foreach ($menus as $menu)
				{
					$menulist[ $menutypes[ $menu->typeID ] ][ $menu->id ] = str_repeat('-', $menu->level - 1) . ' ' . Yii::tr($menu->name);
				}
				$this->data('menudrop', $menulist);
			}
		}
	}