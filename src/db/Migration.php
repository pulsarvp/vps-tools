<?php

	namespace vps\tools\db;

	use vps\tools\helpers\StringHelper;
	use yii\base\InvalidConfigException;
	use yii\db\Exception;
	use yii\db\Query;

	/**
	 * @inheritdoc
	 *
	 * @property-read string|null $dbName
	 */
	class Migration extends \yii\db\Migration
	{
		/**
		 * @inheritdoc
		 */
		public function addForeignKey ($name, $table, $columns, $refTable, $refColumns, $delete = "CASCADE", $update = "CASCADE")
		{
			parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
		}

		/**
		 * Creates database view.
		 *
		 * @param string $name View name.
		 * @param Query  $query Query that is used to create view.
		 * @param bool   $replace Whether to replace existing view with the same name.
		 *
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
		 * Server encoding checking
		 *
		 * @param string $encoding
		 * @param bool   $exception
		 *
		 * @return bool
		 * @throws InvalidConfigException
		 */
		public function checkCollation ($encoding = 'utf8', $exception = true)
		{
			$variables = [
				'character_set_client',
				'character_set_connection',
				'character_set_database',
				'character_set_results',
				'character_set_server',
				'character_set_system',
				'collation_connection',
				'collation_database',
				'collation_server'
			];
			foreach ($variables as $variable)
			{
				$sql = "show variables like '" . $variable . "'";
				$value = $this->db->createCommand($sql)->queryOne();
				$pos = StringHelper::pos($value[ 'Value' ], $encoding);
				if ($pos !== 0)
				{
					if (!$exception)
						return false;
					else
						throw new InvalidConfigException ("Parameter $variable does not match $encoding.");
				}
			}

			return true;
		}

		/**
		 * Check if provided engine is supported and enabled.
		 *
		 * @param string $name Engine name.
		 * @param bool   $default Whether to check if engine is default.
		 * @param bool   $exception Whether to throw exception on error.
		 *
		 * @return bool True in case of engine is enabled and (in case of default is true) default. Otherwise exception is thrown (id exception is true) or false returned.
		 * @throws \yii\db\Exception
		 */
		public function checkEngine (string $name = 'InnoDB', bool $default = true, bool $exception = true)
		{
			$engines = $this->db->createCommand("SHOW ENGINES")->queryAll();
			foreach ($engines as $engine)
			{
				if (strcasecmp($engine[ 'Engine' ], $name) == 0)
				{
					switch ($engine[ 'Support' ])
					{
						case 'DEFAULT':
							return true;

						case 'YES':
							if ($default)
							{
								if ($exception)
									throw new Exception("Engine $name is enabled but not default.");
								else
									return false;
							}
							else
								return true;

						case 'DISABLED':
							if ($exception)
								throw new Exception("Engine $name is supported but disabled.");
							else
								return false;

						default:
							if ($exception)
								throw new Exception("Engine $name is not supported.");
							else
								return false;
					}
				}
			}

			if ($exception)
				throw new Exception("Engine $name not found in the list of database engines.");
			else
				return false;
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
		 * Creates enum column.
		 *
		 * @param array $values
		 * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized.
		 */
		public function enum ($values)
		{
			return $this->getDb()->getSchema()->createColumnSchemaBuilder("enum('" . implode("','", $values) . "')");
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

		/**
		 * Checks whether column for a table  exist.
		 *
		 * @param string $table
		 * @param string $column
		 * @return bool
		 */
		public function hasColumn ($table, $column)
		{
			$schema = $this->db->getTableSchema($table);

			return isset($schema->columns[ $column ]);
		}
	}