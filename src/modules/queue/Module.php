<?php
	namespace vps\tools\modules\queue;

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;
	use Yii;

	/**
	 * Class Module Queue
	 *
	 * @package vps\tools\modules\queue
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @var string the namespace that controller classes are in
		 */
		public $controllerNamespace = 'vps\tools\modules\queue\controllers';

		public $title = "View queue";

		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			$app->setAliases([ '@queueViews' => __DIR__ . '/views' ]);
			$app->getUrlManager()->addRules([
				[ 'class'   => 'yii\web\UrlRule',
				  'pattern' => 'queue/<action:[\w\-]+>',
				  'route'   => $this->id . '/queue/<action>'
				],
			], false);

			// Add module I18N category.
			ConfigurationHelper::addTranslation('queue', [ 'queue' => 'queue.php' ], __DIR__ . '/messages');

			$this->title = Yii::tr($this->title, [], 'queue');
		}
	}