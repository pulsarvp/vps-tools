<?php
	namespace vps\tools\db;

	use yii\db\Query;

	/**
	 * @inheritdoc
	 *
	 * @property-read string|null $dbName
	 */
	class Migration extends \yii\db\Migration
	{

		/**
		 * Creates database view.
		 *
		 * @param string $name View name.
		 * @param Query  $query Query that is used to create view.
		 * @param bool   $replace Whether to replace existing view with the same name.
		 * @throws \yii\db\Exception
		 * @see dropView
		 */
		public function createView ($name, Query $query, $replace = true)
		{
			echo "    > create table $name ...";
			$time = microtime(true);

			$sql = 'CREATE' . ( $replace ? ' OR REPLACE' : '' ) . ' VIEW ' . $this->db->quoteTableName($name) . ' AS ' . $query->createCommand()->getRawSql();
			$this->db->createCommand($sql)->execute();

			echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
		}

		/**
		 * Drops view by name.
		 *
		 * @param string $name
		 *
		 * @see createView
		 */
		public function dropView ($name)
		{
			echo "    > drop view $name ...";
			$time = microtime(true);
			$this->db->createCommand('DROP VIEW IF EXISTS ' . $this->db->quoteTableName($name))->execute();
			echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
		}

		/**
		 * Find all foreign keys names for specific table and column.
		 *
		 * @param string      $table
		 * @param string|null $column
		 *
		 * @return string[]
		 */
		public function findForeignKeys ($table, $column = null)
		{
			$query = ( new  Query )
				->select('CONSTRAINT_NAME')
				->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
				->where([
					'TABLE_SCHEMA' => $this->getDbName(),
					'TABLE_NAME'   => $table
				]);
			if (!is_null($column))
				$query->andWhere([ 'COLUMN_NAME' => $column ]);

			return $query->column();
		}

		/**
		 * Loads queries from file and executes them. Each query should be on
		 * new line just in case.
		 *
		 * @param string $path Path to the file.
		 *
		 * @throws \Exception
		 * @throws \yii\db\Exception
		 */
		public function fromFile ($path)
		{
			if (file_exists($path) and is_readable($path))
			{
				echo "    > loading queries from file $path ...";
				$time = microtime(true);

				$rows = file($path, FILE_SKIP_EMPTY_LINES);
				foreach ($rows as $row)
					$this->db->createCommand($row)->execute();

				echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
			}
			else
				throw new \Exception ('Cannot open file ' . $path . ' for reading.');
		}

		/**
		 * Sets foreign key check to 1 or 0.
		 *
		 * @param bool $check
		 */
		public function foreignKeyCheck ($check = true)
		{
			$check = intval(boolval($check));
			$this->db->createCommand("SET FOREIGN_KEY_CHECKS=$check")->execute();
		}

		/**
		 * Gets database name via dbname parameter from dsn.
		 *
		 * @return string|null
		 */
		public function getDbName ()
		{
			if ($this->db->getDriverName() == 'mysql')
			{
				preg_match("/dbname=([^;]*)/", $this->db->dsn, $match);
				if (isset($match[ 1 ]))
					return $match[ 1 ];
			}

			return null;
		}
	}