<?php

    namespace vps\tools\modules\queue\controllers;

    use app\base\Controller;
    use vps\tools\modules\queue\models\search\QueueInformationSearch;
    use Yii;
    use vps\tools\modules\queue\redis\QueueInformation;
    use yii\grid\GridView;
    use yii\widgets\DetailView;

    class QueueRedisController extends Controller
    {
        public function actionIndex ()
        {
            $this->setTitle(Yii::tr('Queue management', [], 'queue'));

            $queueInformation = new QueueInformation(Yii::$app->redis, Yii::$app->queue);

            $this->data('detailView', $this->getDetailView($queueInformation->getInformation()));

            $searchModel = new QueueInformationSearch([
                'queueJobs' => $queueInformation->getJobs()
            ]);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $this->data('gridView', $this->getGridView($searchModel, $dataProvider));

            $this->_tpl = '@queueViews/queue-redis/index';
        }

        /**
         * @param $information
         * @return string
         */
        private function getDetailView ($information)
        {
            return DetailView::widget([
                'model'      => $information,
                'attributes' => [
                    [
                        'attribute' => 'channel',
                        'label'     => Yii::tr('Channel', [], 'queue'),
                    ],
                    [
                        'attribute' => 'ttrDefault',
                        'label'     => Yii::tr('TTR default', [], 'queue'),
                    ],
                    [
                        'attribute' => 'done',
                        'label'     => Yii::tr('Done', [], 'queue'),
                    ],
                    [
                        'attribute' => 'waiting',
                        'label'     => Yii::tr('Waiting', [], 'queue'),
                    ],
                    [
                        'attribute' => 'reserved',
                        'label'     => Yii::tr('Reserved', [], 'queue')
                    ],
                ],
            ]);
        }

        /**
         * @param $searchModel
         * @param $dataProvider
         * @return string
         */
        private function getGridView ($searchModel, $dataProvider)
        {
            return GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'pager'        => [
                    'class' => '\vps\tools\widgets\LinkPager',
                ],
                'columns'      => [
                    'id',
                    [
                        'attribute' => 'status',
                        'value'     => function ($model)
                        {
                            return QueueInformationSearch::statusesRus($model[ 'status' ]);
                        },
                        'filter'    => QueueInformationSearch::statusesRus(),
                    ],
                    'ttr',
                    [
                        'attribute' => 'delay',
                        'format'    => 'datetime',
                        'filter'    => false,
                    ],
                    [
                        'attribute' => 'reserve',
                        'format'    => 'datetime',
                        'filter'    => false,
                    ],
                    [
                        'attribute' => 'attempts',
                        'format'    => 'integer',
                    ],
                    [
                        'attribute' => 'classJob',
                        'value'     => function ($model)
                        {
                            return str_replace('\\', '\\ ', $model[ 'classJob' ]);
                        },
                    ],
                    [
                        'attribute' => 'paramsAsString',
                        'value'     => function ($model)
                        {
                            return str_replace(',', ', ',
                                QueueInformationSearch::paramsAsString($model[ 'paramsJob' ])
                            );
                        },
                    ],
                ],
            ]);
        }
    }