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
		 * Guid menutype
		 *
		 * ```php
		 * menutype =Yii::$app->settings->get( 'backendmain' )
		 * ```
		 */
		public $menutype;

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
				'menutype' => $this->menutype,
				'classUl'  => $this->classUl
			]);
		}
	}