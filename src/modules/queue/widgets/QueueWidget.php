<?php

	namespace vps\tools\modules\queue\widgets;

	use vps\tools\modules\queue\models\Queue;
	use Yii;
	use yii\base\Widget;
	use yii\data\ActiveDataProvider;
	use yii\web\View;

	/**
	 * Class QueueWidget
	 *
	 * @package vps\tools\modules\queue\widgets
	 */
	class QueueWidget extends Widget
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
						],
						'widgets' => [
							'blocks' => [
								'Form' => '\vps\tools\html\Form'
							]
						],
					]
				]
			]);

			Yii::$app->view->registerCss('#queue-list .job {font-family: Menlo, monospace; overflow-wrap: break-word; word-wrap: break-word; max-width: 500px}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{

			$fields = [ 'job', 'ttr', 'delay', 'priority', 'pushed_at', 'reserved_at', 'done_at' ];
			$sort = Yii::$app->request->get('sort');
			$provider = new ActiveDataProvider([
				'query'      => Queue::find(),
				'sort'       => [
					'attributes'   => $fields,
					'defaultOrder' => [
						'pushed_at' => SORT_DESC
					],
					'params'       => [ 'sort' => $sort ]
				],
				'pagination' => [
					'pageSize'       => Yii::$app->settings->get('queue_ui_pagesize', 20),
					'forcePageParam' => false,
					'pageSizeParam'  => false,
				]
			]);

			return $this->renderFile('@queueViews/index.tpl', [
				'title'      => Yii::tr('View queue list', [], 'queue'),
				'fields'     => $fields,
				'queues'     => $provider->models,
				'pagination' => $provider->pagination,
				'sort'       => $provider->sort
			]);
		}

	}