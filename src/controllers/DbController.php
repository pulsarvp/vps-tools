<?php
	namespace vps\tools\controllers;

	use vps\tools\helpers\Console;
	use Yii;

	/**
	 * Allows to manipulate database with console.
	 */
	class DbController extends \yii\console\Controller
	{
		public $defaultAction = 'tables';

		/**
		 * List all views. This method is needed in order to match the database collection if it was changed.
		 */
		public function actionRecreateViews ()
		{
			$db = Yii::$app->db;
			$views = $this->getViews();

			foreach ($views as $view)
			{
				$info = $db->createCommand("SHOW CREATE VIEW `$view`")->queryAll();
				$sql = $info[ 0 ][ 'Create View' ];
				preg_match("/.*VIEW `$view` as (.*)/i", $sql, $match);
				if (isset($match[ 1 ]))
				{
					$db->createCommand("CREATE OR REPLACE VIEW `$view` AS " . $match[ 1 ])->execute();
					Console::printColor("View `$view` was recreated.", "green");
				}
				else
				{
					Console::printColor("Cannot find create string for view `$view`.", "red");
				}
			}
		}

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