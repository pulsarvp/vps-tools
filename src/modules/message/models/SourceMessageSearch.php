<?php

	namespace vps\tools\modules\message\models;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2019
	 * @date      2019-01-30
	 */

	use  yii\base\Model;
	use Yii;
	use yii\data\ActiveDataProvider;

	/**
	 * SourceMessageSearch represents the model behind the search form about
	 * `common\modules\translation\models\SourceMessage`.
	 */
	class SourceMessageSearch extends SourceMessage
	{
		public $languages = [];

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			return [
				[ [ 'id' ], 'integer' ],
				[ [ 'category', 'description', 'message', 'languages' ], 'safe' ],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function scenarios ()
		{
			// bypass scenarios() implementation in the parent class
			return Model::scenarios();
		}

		/**
		 * Creates data provider instance with search query applied
		 *
		 * @param array $params
		 *
		 * @return ActiveDataProvider
		 */
		public function search ($params)
		{
			$query = SourceMessage::find();
			$languages = Yii::$app->getModule('messages')->languages;
			foreach ($languages as $one)
			{
				$query->leftJoin('{{%message}} as ' . $one, $one . '.id = {{%source_message}}.id and ' . $one . '.language = "' . $one . '"');
			}
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			$this->load($params);
			if (!$this->validate())
			{
				return $dataProvider;
			}
			$query->andFilterWhere([
				'id' => $this->id,
			]);
			$query->andFilterWhere([ 'like', 'category', $this->category ])
				->andFilterWhere([ 'like', 'message', $this->message ]);
			foreach ($languages as $one)
			{
				if (isset($this->languages[ $one ]))
					$query->andFilterWhere([ 'like', $one . '.translation', $this->languages[ $one ] ]);
			}

			return $dataProvider;
		}
	}