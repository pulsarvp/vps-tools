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

			Yii::$app->view->registerCss('.job pre{display:block; overflow:hidden; color:#EBEBEB; padding:.5em; border:0px; background:inherit; font-size:11px;min-width:350px; white-space:pre-wrap; word-wrap:break-word;} .priority {width: 7%;} .priority .priority-edit-buttons {display: none; margin-top: 5px; text-align: right;.priority .error {display: none;}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{

			$fields = [ 'id', 'pid', 'channel', 'ttr', 'delay', 'priority', 'pushed_at', 'reserved_at', 'done_at', 'canceled_at' ];
			$sort = Yii::$app->request->get('sort');
			$filterChannel = Yii::$app->request->get('filterChannel');
			$query = Queue::find();
			if (isset($filterChannel) and $filterChannel != '')
			{
				$query->andWhere([ 'channel' => $filterChannel ]);
			}
			$provider = new ActiveDataProvider([
				'query'      => $query,
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
			foreach ($provider->models as $queue)
			{
				$json = json_decode($queue->job);
				$queue->job = str_replace([ "\\\\", "\/" ], [ "\\", "/" ], json_encode($json, JSON_PRETTY_PRINT));
			}
			$channels = Queue::find()->select('channel')->distinct()->column();

			return $this->renderFile('@queueViews/index.tpl', [
				'title'         => Yii::tr('View queue list', [], 'queue'),
				'fields'        => $fields,
				'queues'        => $provider->models,
				'pagination'    => $provider->pagination,
				'sort'          => $provider->sort,
				'channels'      => $channels,
				'filterChannel' => $filterChannel
			]);
		}

	}