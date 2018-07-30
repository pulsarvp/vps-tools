<?php

	namespace tests\components;

	class BaseModelTest extends \PHPUnit\Framework\TestCase
	{
		private $_modelClass = 'tests\models\BaseTest';

		public function setUp ()
		{
			parent::setUp();
		}

		public function testToogle ()
		{
			$class = $this->_modelClass;
			$object = $class::find()->orderBy([ 'order' => SORT_ASC ])->one();
			$object->toggleAttribute('flag');
			$this->assertFalse($object->flag == 0);
			$this->assertTrue($object->flag == 1);
			$object->toggleAttribute('flag');
			$this->assertFalse($object->flag == 1);
			$this->assertTrue($object->flag == 0);
			$object->toggleAttribute('flag', false);
			$object = $class::find()->orderBy([ 'order' => SORT_ASC ])->one();
			$this->assertFalse($object->flag == 1);
			$this->assertTrue($object->flag == 0);
		}

	}