<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-07-12
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use Yii;
	use yii\base\Widget;
	use yii\web\View;

	class JsWidget extends Widget
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
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			return $this->renderFile(__DIR__ . '/views/assets/js.tpl', [
				'assets_js'     => Yii::$app->settings->get('assets_js'),
				'assets_js_use' => Yii::$app->settings->get('assets_js_use')
			]);
		}
	}