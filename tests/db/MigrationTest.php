<?php

	namespace tests\db;

	use PHPUnit\Framework\TestCase;
	use vps\tools\db\Migration;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\db\Exception;

	class MigrationTest extends TestCase
	{
		public function testCheckCollation ()
		{
			$variables = [
				'character_set_client'     => 'utf8mb4',
				'character_set_connection' => 'utf8',
				'character_set_database'   => 'utf8',
				'character_set_results'    => 'utf8mb4',
				'character_set_server'     => 'utf8',
				'collation_connection'     => 'utf8_general_ci',
				'collation_database'       => 'utf8_general_ci',
				'collation_server'         => 'utf8_general_ci'
			];
			$old = [];
			foreach ($variables as $variable => $coll)
			{
				$sql = "show variables like '" . $variable . "'";
				$old[] = Yii::$app->db->createCommand($sql)->queryOne();
			}

			foreach ($variables as $variable => $coll)
			{
				$sql = "SET " . $variable . "=" . $coll;

				Yii::$app->db->createCommand($sql)->execute();
			}

			$migration = new Migration();
			$this->assertFalse($migration->checkCollation('latin1', false));
			$this->assertTrue($migration->checkCollation('utf8', false));

			$this->expectException(InvalidConfigException::class);
			$migration->checkCollation('latin1');

			foreach ($old as $variable)
			{
				$sql = "SET " . $variable[ 'Variable_name' ] . "=" . $variable[ 'Value' ];
				Yii::$app->db->createCommand($sql)->execute();
			}
		}

		public function testCheckEngine ()
		{
			$enginesResult = Yii::$app->db->createCommand("SHOW ENGINES")->queryAll();

			$engineDefault = null;
			$engineYes = null;
			$engineNo = null;
			$engineDisabled = null;

			foreach ($enginesResult as $result)
			{
				switch ($result[ 'Support' ])
				{
					case 'DEFAULT':
						$engineDefault = $result[ 'Engine' ];
						break;

					case 'YES':
						$engineYes = $result[ 'Engine' ];
						break;

					case 'DISABLED':
						$engineDisabled = $result[ 'Engine' ];
						break;

					case 'NO':
						$engineNo = $result[ 'Engine' ];
						break;
				}
			}

			$migration = new Migration();

			if ($engineDefault)
			{
				$this->assertTrue($migration->checkEngine($engineDefault));
				$this->assertTrue($migration->checkEngine($engineDefault, false));
				$this->assertTrue($migration->checkEngine($engineDefault, false, false));
			}

			if ($engineYes)
			{
				$this->expectException(Exception::class);
				$this->expectExceptionMessage("Engine $engineYes is enabled but not default.");
				$migration->checkEngine($engineYes);

				$this->assertFalse($migration->checkEngine($engineYes, true, false));
				$this->assertTrue($migration->checkEngine($engineYes, false));
				$this->assertTrue($migration->checkEngine($engineYes, false, false));
			}

			if ($engineNo)
			{
				$this->expectException(Exception::class);
				$this->expectExceptionMessage("Engine $engineNo is not supported.");
				$migration->checkEngine($engineNo);

				$this->assertFalse($migration->checkEngine($engineNo, false, false));
			}

			if ($engineDisabled)
			{
				$this->expectException(Exception::class);
				$this->expectExceptionMessage("Engine $engineDisabled is supported but disabled.");
				$migration->checkEngine($engineDisabled);

				$this->assertFalse($migration->checkEngine($engineDisabled, false, false));
			}

			$this->expectException(Exception::class);
			$this->expectExceptionMessage("Engine _random_ not found in the list of database engines.");
			$migration->checkEngine("_random_");

			$this->assertFalse($migration->checkEngine("_random_", false, false));
		}

		public function testFindForeignKeys ()
		{
			$migration = new Migration();

			$migration->createTable("test_ffk", [
				"id"   => $migration->primaryKey(),
				"name" => $migration->integer()
			]);

			$migration->createTable("test_ffk2", [
				"keyID" => $migration->integer(),
				"name"  => $migration->integer(),
				"name2" => $migration->integer()
			]);

			$migration->addForeignKey("test_ffk2_key", "test_ffk2", "keyID", "test_ffk", "id", "CASCADE", "CASCADE");
			$migration->createIndex('name', 'test_ffk', 'name');
			$migration->addForeignKey("test_ffk3_key", "test_ffk2", "name2", "test_ffk", "name", "CASCADE", "CASCADE");

			$keys = $migration->findForeignKeys('test_ffk2');
			$expected = [ 'test_ffk2_key', 'test_ffk3_key' ];
			sort($keys);
			sort($expected);
			$this->assertEquals($keys, $expected);

			$migration->dropForeignKey('test_ffk3_key', 'test_ffk2');
			$this->assertEquals($migration->findForeignKeys('test_ffk2'), [ 'test_ffk2_key' ]);

			$migration->dropForeignKey('test_ffk2_key', 'test_ffk2');
			$this->assertEquals($migration->findForeignKeys('test_ffk2'), []);
		}

		public function testForeignKeyCheck ()
		{
			$migration = new Migration();

			$migration->createTable("test_fkc", [
				"id"   => $migration->primaryKey(),
				"name" => $migration->string(20)
			]);

			$migration->createTable("test_fkc2", [
				"keyID" => $migration->integer(),
				"name"  => $migration->string(20)
			]);

			$migration->createIndex("key", "test_fkc2", "keyID");
			$migration->addForeignKey("test_fkc2_key", "test_fkc2", "keyID", "test_fkc", "id", "CASCADE", "CASCADE");

			for ($i = 1; $i < 5; $i++)
			{
				$migration->insert("test_fkc", [ "id" => $i, "name" => "item$i" ]);
				$migration->insert("test_fkc2", [ "keyID" => $i, "name" => "key_item$i" ]);
			}

			$migration->foreignKeyCheck(false);
			$migration->foreignKeyCheck(true);
			$this->expectException(\yii\db\IntegrityException::class);
			$migration->dropTable("test_fkc");

			$migration->foreignKeyCheck(false);
			$migration->dropTable("test_fkc");
			$migration->dropTable("test_fkc2");
			$migration->foreignKeyCheck(true);
		}
	}