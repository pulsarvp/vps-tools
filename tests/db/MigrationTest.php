<?php
	namespace tests\db;

	use vps\tools\db\Migration;

	class MigrationTest extends \PHPUnit\Framework\TestCase
	{
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