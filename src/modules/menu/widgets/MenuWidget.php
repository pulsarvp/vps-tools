<?php

	namespace vps\tools\modules\menu\widgets;

	use yii\base\Widget;
	use yii\web\View;

	class MenuWidget extends Widget
	{
		/**
		 * Class for the tag ul
		 *
		 * ```php
		 * classUl = 'nav'
		 * ```
		 */
		public $classUl;
		/**
		 * List menuitem
		 *
		 * ```php
		 * menus = Yii::$app->menu->forType( Yii::$app->settings->get( 'backendmain' ) )
		 * ```
		 */
		public $menus;

		/**
		 * Template the menu tree
		 *
		 * ```php
		 * tree = true
		 * ```
		 */
		public $tree = false;

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
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if ($this->tree)
				$template = '@menuViews/menu.tree.tpl';
			else
				$template = '@menuViews/menu.tpl';


			return $this->renderFile($template, [
				'menus'   => $this->menus,
				'classUl' => $this->classUl
			]);
		}
	}