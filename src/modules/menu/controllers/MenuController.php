<?php

	namespace vps\tools\modules\menu\controllers;

	use app\base\Controller;
	use vps\tools\helpers\Url;
	use vps\tools\modules\menu\models\Menu;
	use vps\tools\modules\menu\models\MenuType;
	use Yii;
	use yii\filters\AccessControl;
	use yii\helpers\Json;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-09-26
	 */
	class MenuController extends Controller
	{
		public function behaviors ()
		{
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow'         => true,
							'actions'       => [ 'add', 'edit', 'delete', 'visible' ],
							'roles'         => [ '@' ],
							'denyCallback'  => function ($rule, $action)
							{
								Yii::$app->notification->errorToSession(Yii::tr('You have no permissions.'));
								$this->redirect([ '/user/index' ]);
							},
							'matchCallback' => function ($rule, $action)
							{
								if (!Yii::$app->user->identity->active or !( Yii::$app->user->can('admin') or Yii::$app->user->can('admin_menu') ))
								{
									Yii::$app->notification->errorToSession(Yii::tr('You have no permissions.', [], 'user'));
									$this->redirect(Url::toRoute([ '/site/index' ]));
								}
								else
									return true;
							}
						]
					],
				],
			];
		}

		/**
		 * Removes old menu items and saves new menu items
		 */
		public function actionAdd ()
		{

			$menu = new Menu();
			$this->_tpl = '@menuViews/add';
			$this->setTitle(Yii::tr('Add menu item', [], 'menu'));
			$type = MenuType::findOne(Yii::$app->request->get('type'));
			if ($type == null)
				return;
			$root = null;
			$parentId = Yii::$app->request->get('parentID');
			if (!is_null($parentId))
			{
				$root = Menu::findOne([ 'id' => $parentId, 'typeID' => $type->id ]);
				$this->setTitle(Yii::tr('Add menu subitem to {title}', [ 'title' => $root->title ], 'menu'));
			}

			if (Yii::$app->request->isPost)
			{
				$post = Yii::$app->request->post('Menu');

				if (is_null($root))
				{
					$root = Menu::findOne([ 'title' => $type->guid . '_ROOT', 'typeID' => $type->id ]);
					if ($root == null)
					{
						$root = new Menu([ 'title' => $type->guid . '_ROOT', 'typeID' => $type->id ]);
						$root->makeRoot();
					}
				}

				$menu->setAttributes([
					'title'  => $post[ 'title' ],
					'url'    => $post[ 'url' ],
					'path'   => $post[ 'path' ],
					'typeID' => $type->id
				]);

				$menu->appendTo($root);
				Yii::$app->notification->messageToSession(Yii::tr('Menu item was saved.', [], 'menu'));
				$this->redirect(Url::toRoute([ '/menu/index', 'type' => $type->id ]));
			}
			$this->data('model', $menu);
		}

		/**
		 * Removes old menu items and saves new menu items
		 */
		public function actionEdit ()
		{
			$id = Yii::$app->request->get('id');
			$menu = Menu::findOne($id);
			if ($menu == null)
			{
				Yii::$app->notification->messageToSession(Yii::tr('Menu item not found.', [], 'menu'));
				$this->redirect(Yii::$app->request->referrer);
			}
			if (Yii::$app->request->isPost)
			{
				$post = Yii::$app->request->post('Menu');

				$menu->updateAttributes([
					'title' => $post[ 'title' ],
					'url'   => $post[ 'url' ],
					'path'  => $post[ 'path' ]
				]);

				$menu->save();
				Yii::$app->notification->messageToSession(Yii::tr('Menu item was saved.', [], 'menu'));
				$this->redirect(Url::toRoute([ '/menu/index', 'type' => $menu->typeID ]));
			}
			$this->data('model', $menu);
			$this->setTitle(Yii::tr('Edit menu item', [], 'menu'));
			$this->_tpl = '@menuViews/add';
		}

		/**
		 * Removes old menu items and saves new menu items
		 */
		public function actionDelete ()
		{
			if (Yii::$app->request->isAjax)
			{
				$id = Yii::$app->request->post('id');
				$menu = Menu::findOne($id);
				if ($menu !== null)
				{
					if (!$menu->deleteWithChildren())
						echo Json::encode(current($menu->firstErrors));
					else
						echo 0;
				}
			}
			Yii::$app->end();
		}

		/**
		 * This action is for AJAX request. It is update role to user
		 */
		public function actionVisible ()
		{
			if (Yii::$app->request->isAjax)
			{
				$id = Yii::$app->request->post('id');
				$menu = Menu::findOne($id);
				if ($menu !== null)
				{
					$menu->setAttribute('visible', $menu->visible == 1 ? 0 : 1);
					if (!$menu->save())
						echo Json::encode(current($menu->firstErrors));
					else
						echo $menu->visible;
				}
			}
			Yii::$app->end();
		}
	}