<?php

	namespace tests\components;

	use Yii;

	class BaseOrderModelTest extends \PHPUnit\Framework\TestCase
	{
		private $_modelClass = 'tests\models\BaseTest';

		public function setUp ()
		{
			parent::setUp();
		}

		public function testAdd ()
		{
			$class = $this->_modelClass;
			$object = new $class();
			$object->save();
			$this->assertFalse($object->order == 1);
			$this->assertFalse($object->order == 5);
			$this->assertTrue($object->order == 4);
		}

		public function testDelete ()
		{
			$class = $this->_modelClass;
			$objectOld = $class::find()->orderBy([ 'order' => SORT_ASC ])->one();
			$objectOld->delete();
			$object = $class::find()->orderBy([ 'order' => SORT_DESC ])->one();
			$this->assertFalse($object->order == 1);
			$this->assertFalse($object->order == 2);
			$this->assertTrue($object->order == 3);
		}

		public function testMove ()
		{
			$class = $this->_modelClass;
			$object = $class::find()->orderBy([ 'order' => SORT_ASC ])->one();
			$object->moveUp();
			$this->assertFalse($object->order == 1);
			$this->assertFalse($object->order == 3);
			$this->assertTrue($object->order == 2);
			$object->moveDown();
			$this->assertFalse($object->order == 2);
			$this->assertFalse($object->order == 3);
			$this->assertTrue($object->order == 1);
		}

		public function testRebuild ()
		{
			$class = $this->_modelClass;
			Yii::$app->db->createCommand("INSERT INTO `base_test` (`uuid`, `order`, `createDT`, `dt`) VALUES ('980e2db4-75af-41a3-946c-5eb22e054246', 2, '2018-07-09 16:41:10', NULL)")->execute();
			$class::rebuildOrder();
			$object = $class::find()->where([ 'uuid' => '980e2db4-75af-41a3-946c-5eb22e054246' ])->one();
			$this->assertFalse($object->order == 1);
			$this->assertFalse($object->order == 2);
			$this->assertTrue($object->order == 3);
		}

	}