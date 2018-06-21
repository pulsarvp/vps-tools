<?php

	namespace vps\tools\modules\log\controllers;

	use app\base\Controller;
	use common\models\User;
	use vps\tools\helpers\TimeHelper;
	use vps\tools\modules\log\dictionaries\LogType;
	use vps\tools\modules\log\models\Log;
	use Yii;
	use yii\data\ActiveDataProvider;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 208
	 * @date      2018-05-24
	 */
	class LogController extends Controller
	{

		public function actionIndex ()
		{
			$get = Yii::$app->request->get();

			$query = Log::find();
			if (isset($get[ 'type' ]))
			{
				$query->andWhere([ 'type' => $get[ 'type' ] ]);
				$this->data('type', $get[ 'type' ]);
			}
			else
				$this->data('type', '');

			if (isset($get[ 'userID' ]))
			{
				$query->andWhere([ 'userID' => $get[ 'userID' ] ]);
				$this->data('userID', $get[ 'userID' ]);
			}
			else
				$this->data('userID', '');

			if (isset($get[ 'from' ]))
			{
				$query->andWhere([ '>=', 'dt', $get[ 'from' ] ]);
				$this->data('from', $get[ 'from' ]);
			}

			if (isset($get[ 'to' ]))
			{
				$query->andWhere([ '>=', 'dt', $get[ 'to' ] ]);
				$this->data('to', $get[ 'to' ]);
			}

			if (isset($get[ 'search' ]))
			{
				$query->andWhere([ 'or', [ 'like', 'email', $get[ 'search' ] ], [ 'like', 'action', $get[ 'search' ] ], [ 'like', 'url', $get[ 'search' ] ] ]);
				$this->data('search', $get[ 'search' ]);
			}

			$provider = new ActiveDataProvider([
				'query'      => $query,
				'sort'       => [
					'attributes'   => [
						'userID',
						'email',
						'type',
						'action',
						'url',
						'dt'
					],
					'defaultOrder' => [
						'dt' => SORT_DESC
					]
				],
				'pagination' => [
					'pageSize'       => Yii::$app->settings->get('page_size_object', 20),
					'forcePageParam' => false,
					'pageSizeParam'  => false,
					'urlManager'     => new \yii\web\UrlManager([
						'enablePrettyUrl' => true,
						'showScriptName'  => false,
						'rules'           => [
							'log/page/<page>' => 'log/index'
						]
					])
				]
			]);
			$ids = Log::find()->select('userID')->where('userID IS NOT NULL')->groupBy([ 'userID' ])->column();

			$users = User::find()->where([ 'IN', 'id', $ids ])->all();

			$this->data('users', $users);
			$this->data('models', $provider->models);
			$this->data('pagination', $provider->pagination);
			$this->data('sort', $provider->sort);

			$this->data('types', [ LogType::INFO, LogType::WARNING, LogType::ERROR ]);
			$this->setTitle(Yii::tr('Logs', [], 'log'));
			$this->_tpl = '@logViews/index';
		}

		public function actionJson ()
		{
			$post = Yii::$app->request->post();

			$dt = date(TimeHelper::$dtFormat, $post[ 'dt' ]);
			$log = Log::find()->where([ 'type' => $post[ 'type' ], 'dt' => $dt, 'userID' => $post[ 'userID' ] ])->one();

			$result = [];
			if ($log != null)
			{
				$result = $log->getAttributes();
				$result[ 'server' ] = json_decode($log->server, true);
				$result[ 'session' ] = json_decode($log->session, true);
				$result[ 'cookie' ] = json_decode($log->cookie, true);
				$result[ 'cookie' ] = json_decode($log->cookie, true);
				$result[ 'post' ] = json_decode($log->post, true);
				$result[ 'dt' ] = Yii::$app->formatter->asDatetime($log->dt);
			}

			echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			exit();
		}

	}