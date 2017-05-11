<?php
	namespace vps\tools\controllers;

	use Yii;

	/**
	 * Allows to manipulate database with console.
	 */
	class DbController extends \yii\console\Controller
	{
		public $defaultAction = 'tables';

		/**
		 * List all tables in database.
		 */
		public function actionTables ()
		{
			echo "Tables:\n";
			echo implode("\n", $this->getTables());

			echo "Views:\n";
			echo implode("\n", $this->getViews());
		}

		/**
		 * List all views in database.
		 */
		public function actionViews ()
		{
			echo implode("\n", $this->getViews());
		}

		private function getTables ($type = 'BASE TABLE')
		{
			$tables = [];
			$data = Yii::$app->db->createCommand('show full tables')->queryAll();
			foreach ($data as $item)
			{
				$item = array_values($item);
				if ($item[ 1 ] == $type)
					$tables[] = $item[ 0 ];
			}
			sort($tables);

			return $tables;
		}

		private function getViews ()
		{
			return $this->getTables('VIEW');
		}
	}