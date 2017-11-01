<?php

	namespace vps\tools\modules\menu\controllers;

	use app\base\Controller;
	use vps\tools\helpers\Url;
	use vps\tools\modules\menu\models\Menu;
	use vps\tools\modules\menu\models\MenuType;
	use Yii;
	use yii\helpers\Json;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-09-26
	 */
	class MenuController extends Controller
	{

		/**
		 * Removes old menu items and saves new menu items
		 */
		public function actionAdd ()
		{

			$menu = new Menu();
			$this->_tpl = '@menuViews/add';
			if (Yii::$app->request->isPost)
			{
				$type = MenuType::findOne(Yii::$app->request->get('type'));
				if ($type == null)
					return;

				$post = Yii::$app->request->post('Menu');

				$parentId = Yii::$app->request->get('parentID');
				if (!is_null($parentId))
				{
					$root = Menu::findOne([ 'id' => $parentId, 'typeID' => $type->id ]);
				}
				else
				{
					$root = Menu::findOne([ 'name' => $type->guid . '_ROOT', 'typeID' => $type->id ]);
					if ($root == null)
					{
						$root = new Menu([ 'name' => $type->guid . '_ROOT', 'typeID' => $type->id ]);
						$root->makeRoot();
					}
				}

				$menu->setAttributes([
					'name'   => $post[ 'name' ],
					'url'    => $post[ 'url' ],
					'path'   => $post[ 'path' ],
					'typeID' => $type->id
				]);

				$menu->appendTo($root);
				Yii::$app->notification->messageToSession(Yii::tr('Menu item was saved.', [], 'menu'));
				$this->redirect(Url::toRoute([ '/menu/index', 'type' => $type->id ]));
			}
			$this->setTitle(Yii::tr('Add item menu', [], 'menu'));
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
					'name' => $post[ 'name' ],
					'url'  => $post[ 'url' ],
					'path' => $post[ 'path' ]
				]);

				$menu->save();
				Yii::$app->notification->messageToSession(Yii::tr('Menu item was saved.', [], 'menu'));
				$this->redirect(Url::toRoute([ '/menu/index', 'type' => $menu->typeID ]));
			}
			$this->data('model', $menu);
			$this->setTitle(Yii::tr('Edit item menu', [], 'menu'));
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
					$visible = $menu->visible == 1 ? 0 : 1;
					$menu->setAttribute('visible', $visible);
					if (!$menu->save())
						echo Json::encode(current($menu->firstErrors));
					else
						echo $visible;
				}
			}
			Yii::$app->end();
		}
	}