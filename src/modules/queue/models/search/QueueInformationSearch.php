<?php

    namespace vps\tools\modules\queue\models\search;

    use Yii;
    use vps\tools\modules\queue\redis\QueueInformation;
    use yii\base\Model;
    use yii\data\ArrayDataProvider;

    class QueueInformationSearch extends Model
    {
        /** @var  array */
        public $queueJobs;

        public $id;
        public $status;
        public $ttr;
        public $delay;
        public $reserve;
        public $attempts;
        public $classJob;
        public $paramsJob;

        public static $statuses = [
            QueueInformation::STATUS_WAITING  => 'Waiting',
            QueueInformation::STATUS_RESERVED => 'Reserved',
            QueueInformation::STATUS_DONE     => 'Done',
            QueueInformation::STATUS_DELAYED  => 'Delayed',
        ];

        /**
         * @param bool|int $id
         * @return array
         */
        public static function statusesRus ($id = false)
        {
            $items = [];
            foreach (static::$statuses as $key => $status)
            {
                $items [ $key ] = Yii::tr($status, [], 'queue');
            }

            if ($id !== false)
            {
                return $items[ $id ];
            }

            return $items;
        }

        /**
         * @inheritdoc
         */
        public function rules ()
        {
            return [
                [ [ 'id', 'status', 'ttr', 'classJob', 'attempts' ], 'integer' ],
                [ [ 'delay', 'reserve' ], 'safe' ],

            ];
        }

        public function attributeLabels ()
        {
            return [
                'id'             => Yii::tr('Id', [], 'queue'),
                'status'         => Yii::tr('Status', [], 'queue'),
                'ttr'            => Yii::tr('TTR', [], 'queue'),
                'delay'          => Yii::tr('Delay time', [], 'queue'),
                'reserve'        => Yii::tr('Reserve time', [], 'queue'),
                'attempts'       => Yii::tr('Attempts', [], 'queue'),
                'classJob'       => Yii::tr('Class job', [], 'queue'),
                'paramsAsString' => Yii::tr('Params', [], 'queue'),
            ];
        }

        /**
         * @param $params
         * @return string
         */
        public static function paramsAsString ($params)
        {
            return json_encode($params);
        }

        /**
         * Creates data provider instance with search query applied
         *
         * @param array $params
         *
         * @return ArrayDataProvider
         */
        public function search ($params)
        {
            $this->load($params);

            if (!empty($this->status))
            {
                $this->queueJobs = array_filter($this->queueJobs, function ($value)
                {
                    return $value[ 'status' ] == $this->status;
                });
            }

            if (!empty($this->id))
            {
                $this->queueJobs = array_filter($this->queueJobs, function ($value)
                {
                    return $value[ 'id' ] == $this->id;
                });
            }

            if (!empty($this->attempts))
            {
                $this->queueJobs = array_filter($this->queueJobs, function ($value)
                {
                    return $value[ 'attempts' ] == $this->attempts;
                });
            }

            if (!empty($this->ttr))
            {
                $this->queueJobs = array_filter($this->queueJobs, function ($value)
                {
                    return $value[ 'ttr' ] == $this->ttr;
                });
            }

            if (!empty($this->classJob))
            {
                $this->queueJobs = array_filter($this->queueJobs, function ($value)
                {
                    $result = stripos($value[ 'classJob' ], $this->classJob);
                    if ($result === false)
                    {
                        return false;
                    }

                    return true;
                });
            }

            $provider = new ArrayDataProvider([
                'allModels'  => $this->queueJobs,
                'sort'       => [
                    'attributes' => [ 'id', 'status', 'ttr', 'delay', 'reserve', 'classJob' ],
                ],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);

            return $provider;
        }

    }