<?php

	namespace vps\tools\modules\menu\widgets;

	use vps\tools\modules\menu\models\Menu;
	use vps\tools\modules\menu\models\MenuType;
	use Yii;
	use yii\base\Widget;
	use yii\web\View;

	class MenuAdminWidget extends Widget
	{
		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();
			$this->view = new View([
				'renderers' => [
					'tpl' => [
						'class'   => 'yii\smarty\ViewRenderer',
						'imports' => [
							'Html' => '\vps\tools\helpers\Html',
							'Url'  => '\vps\tools\helpers\Url'
						]
					]
				]
			]);
			Yii::$app->view->registerCss('#menu-list .value {font-family: Menlo, monospace; overflow-wrap: break-word; word-wrap: break-word; max-width: 500px}.depth-2 {padding-left:20px !important;}.depth-3 {padding-left:30px !important;}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$menuTypes = MenuType::find()->orderBy('guid')->all();
			$menus = [];
			$type = Yii::$app->request->get('type');
			if (!is_null($type))
			{
				$type = MenuType::findOne($type);
				$root = Menu::findOne([ 'title' => $type->guid . '_ROOT', 'typeID' => $type->id ]);
				$menus = $root->children()->all();
			}

			return $this->renderFile('@menuViews/index.tpl', [
				'title'     => Yii::tr('Manage menu', [], 'menu'),
				'menuTypes' => $menuTypes,
				'type'      => $type,
				'menus'     => $menus
			]);
		}
	}