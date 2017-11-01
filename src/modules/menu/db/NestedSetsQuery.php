<?php

	namespace vps\tools\modules\menu\db;

	use creocoder\nestedsets\NestedSetsQueryBehavior;

	class NestedSetsQuery extends \yii\db\ActiveQuery
	{
		public function behaviors ()
		{
			return [
				NestedSetsQueryBehavior::className(),
			];
		}
	}

	?>