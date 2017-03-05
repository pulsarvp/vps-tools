<?php
	namespace vps\tools\modules\notification\widgets;

	use yii\base\Widget;
	use yii\web\View;

	class NotificationWidget extends Widget
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
						'class' => 'yii\smarty\ViewRenderer'
					]
				]
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			return $this->renderFile(__DIR__ . '/../views/index.tpl');
		}
	}