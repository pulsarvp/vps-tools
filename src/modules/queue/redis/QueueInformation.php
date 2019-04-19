<?php

    namespace vps\tools\modules\queue\redis;

    use yii\queue\redis\Queue;
    use yii\redis\Connection;

    class QueueInformation
    {
        const STATUS_WAITING  = 1;
        const STATUS_RESERVED = 2;
        const STATUS_DONE     = 3;
        const STATUS_DELAYED  = 4;

        /** @var  Connection */
        private $redis;

        /** @var  Queue */
        private $queue;

        /** @var  string */
        private $channel;

        /**
         * QueueInformation constructor.
         * @param Connection $redis
         * @param Queue      $queue
         */
        public function __construct (Connection $redis, Queue $queue)
        {
            $this->redis = $redis;
            $this->queue = $queue;

            $this->channel = $this->queue->channel;
        }

        /**
         * Возвращает информацию об очереди
         * @return array
         */
        public function getInformation ()
        {
            $prefix = $this->channel;
            $waiting = $this->queue->redis->llen("$prefix.waiting");
            $delayed = $this->queue->redis->zcount("$prefix.delayed", '-inf', '+inf');
            $reserved = $this->queue->redis->zcount("$prefix.reserved", '-inf', '+inf');
            $total = $this->queue->redis->get("$prefix.message_id");
            $done = $total - $waiting - $delayed - $reserved;

            return [
                'channel' => $this->queue->channel,
                'ttrDefault' => $this->queue->ttr,
                'done' => $done,
                'waiting' => $waiting,
                'delayed' => $delayed,
                'reserved' => $reserved,
            ];
        }

        /**
         * Возвращает текущие задания очереди
         * @return array
         */
        public function getJobs ()
        {
            $items = array_merge(
                $this->listReserved(),
                $this->listWaiting(),
                $this->listDelayed()
            );

            return $items;
        }

        /**
         * Список ожидания
         * @return array
         */
        private function listWaiting ()
        {
            $items = $this->redis->lrange("$this->channel.waiting", '0', '-1');
            sort($items);

            return $this->addProperties($items, static::STATUS_WAITING);
        }

        /**
         * Список отложенных задач
         * @return array
         */
        private function listDelayed ()
        {
            $items = $this->redis->zrange("$this->channel.delayed", '0', '-1');

            return $this->addProperties($items, static::STATUS_DELAYED);
        }

        /**
         * Список выполняемых задач
         * @return array
         */
        private function listReserved ()
        {
            $items = $this->redis->zrange("$this->channel.reserved", '0', '-1');

            return $this->addProperties($items, static::STATUS_RESERVED);
        }

        /**
         * Добавление свойств в список задач
         *
         * @param      $items
         * @param null $status
         * @return array
         */
        private function addProperties ($items, $status = null)
        {
            $elements = [];

            foreach ($items as $id)
            {
                $ttrAndMessage = $this->extractTtrAndMessages($id);

                $elements [] = [
                    'id'        => $id,
                    'status'    => $status,
                    'ttr'       => $ttrAndMessage[ 'ttr' ],
                    'message'   => $ttrAndMessage[ 'message' ],
                    'delay'     => $this->extractDelay($id),
                    'reserve'   => $this->extractReserve($id),
                    'attempts'  => $this->extractAttempts($id),
                    'classJob'  => $ttrAndMessage[ 'classJob' ],
                    'paramsJob' => $ttrAndMessage[ 'paramsJob' ],
                ];
            }

            return $elements;
        }

        /**
         * Извлечение ограничения времени выполнения и сериализованного задания
         * @param $id
         * @return array
         */
        private function extractTtrAndMessages ($id)
        {
            $text = $this->redis->hget("$this->channel.messages", $id);
            $parts = explode(';', $text, 2);

            $message = isset($parts[ 1 ]) ? $parts[ 1 ] : '';
            $classJob = '';
            $paramsJob = '';

            if (!empty($message))
            {
                $messageJob = $this->queue->unserializeMessage($message)[ 0 ];
                $classJob = get_class($messageJob);
                if(is_object($messageJob)){
                    $paramsJob = get_object_vars($messageJob);
                } else {
                    $paramsJob = [];
                }

            }

            return [
                'ttr'       => $parts[ 0 ],
                'message'   => $message,
                'classJob'  => $classJob,
                'paramsJob' => $paramsJob,
            ];
        }

        /**
         * Извлечение времени задержки выполнения
         * @param $id
         * @return mixed
         */
        private function extractDelay ($id)
        {
            return $this->redis->zscore("$this->channel.delayed", $id);
        }

        /**
         * Извлечение времени резервирования
         * @param $id
         * @return mixed
         */
        private function extractReserve ($id)
        {
            return $this->redis->zscore("$this->channel.reserved", $id);
        }

        /**
         * Извлечение количества попыток
         * @param $id
         * @return mixed
         */
        private function extractAttempts ($id)
        {
            return $this->redis->hget("$this->channel.attempts", $id);
        }
    }
