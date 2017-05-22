<?php
	namespace tests\components;

	use Yii;

	class CategoryManagerTest extends \PHPUnit\Framework\TestCase
	{
		private $_category;
		private $_modelClass = 'tests\models\Category';

		public function setUp ()
		{
			parent::setUp();
			$this->_category = Yii::$app->category;
		}

		public function testAll ()
		{
			$all = $this->_category->all;

			$this->assertInternalType('array', $all);
			$this->assertCount(16, $all);
			$this->assertContainsOnlyInstancesOf($this->_modelClass, $all);
		}

		public function testRoot ()
		{
			$root = $this->_category->root;

			$this->assertInstanceOf($this->_modelClass, $root);
			$this->assertEquals(0, $root->depth);
			$this->assertEquals(1, $root->lft);
			$this->assertEquals('root', $root->guid);
			$this->assertEquals('ROOT', $root->title);
		}

		public function testParent ()
		{
			$class = $this->_modelClass;
			$category = $class::find()->where([ 'guid' => 'child_2_2_1' ])->one();

			// Assert nul if input category is not instance of model class.
			$this->assertNull($this->_category->getParent('adsdas'));

			// Assert same category if depth are the same.
			$this->assertEquals($category->getAttributes(), $this->_category->getParent($category, $category->depth)->getAttributes());

			// Assert null if depth is too big or low.
			$this->assertNull($this->_category->getParent($category, 0));
			$this->assertNull($this->_category->getParent($category, -12));
			$this->assertNull($this->_category->getParent($category, 10));

			// Assert actual parent.
			$parent = $category->parents(1)->one();
			$this->assertEquals($parent->getAttributes(), $this->_category->getParent($category, $category->depth - 1)->getAttributes());
			$parent = $category->parents(2)->one();
			$this->assertEquals($parent->getAttributes(), $this->_category->getParent($category, $category->depth - 2)->getAttributes());
		}

		public function testGet ()
		{
			$class = $this->_modelClass;
			$category = $class::find()->where([ 'id' => 12 ])->one();

			$this->assertNull($this->_category->get(-121));
			$this->assertNull($this->_category->get(0));
			$this->assertNull($this->_category->get('adsad'));
			$this->assertNull($this->_category->get([ 3, 4, 4 ]));

			$this->assertEquals($category->getAttributes(), $this->_category->get(12)->getAttributes());
		}

		public function testGetByGuidPath ()
		{
			$class = $this->_modelClass;

			$this->assertNull($this->_category->getByGuidPath(-121));
			$this->assertNull($this->_category->getByGuidPath(0));
			$this->assertNull($this->_category->getByGuidPath('adsad'));
			$this->assertNull($this->_category->getByGuidPath([ 3, 4, 4 ]));

			$this->assertEquals($class::find()->where([ 'guid' => 'child_2_2_1' ])->one()->getAttributes(), $this->_category->getByGuidPath('root_2:child_2_2:child_2_2_1')->getAttributes());
			$this->assertEquals($class::find()->where([ 'guid' => 'root_1' ])->one()->getAttributes(), $this->_category->getByGuidPath('root_1')->getAttributes());
		}

		public function testExists ()
		{
			$this->assertFalse($this->_category->exists(-121));
			$this->assertFalse($this->_category->exists(0));
			$this->assertFalse($this->_category->exists(1));
			$this->assertFalse($this->_category->exists('adsad'));
			$this->assertFalse($this->_category->exists([ 3, 4, 4 ]));

			$this->assertTrue($this->_category->exists(12));
			$this->assertTrue($this->_category->exists(5));
			$this->assertTrue($this->_category->exists(3));
		}

		public function testReload ()
		{
			$category = clone $this->_category->get(12);
			$category->title = 'Changed';
			$this->assertTrue($category->save());

			$this->assertNotEquals($category->getAttributes(), $this->_category->get(12)->getAttributes());
			$this->_category->reload();
			$this->assertEquals($category->getAttributes(), $this->_category->get(12)->getAttributes());
		}

		public function testGuidPath ()
		{
			$this->assertNull($this->_category->guidPath(-121));
			$this->assertNull($this->_category->guidPath(0));
			$this->assertNull($this->_category->guidPath('adsad'));
			$this->assertNull($this->_category->guidPath([ 3, 4, 4 ]));

			$this->assertEquals('root_2:child_2_2:child_2_2_2', $this->_category->guidPath(4));
			$this->assertEquals('root_3:child_3_1', $this->_category->guidPath(9));
			$this->assertEquals('root_4', $this->_category->guidPath(11));
		}

		public function testTitlePath ()
		{
			$this->_category->init();
			$this->assertNull($this->_category->titlePath(-121));
			$this->assertNull($this->_category->titlePath(0));
			$this->assertNull($this->_category->titlePath('adsad'));
			$this->assertNull($this->_category->titlePath([ 3, 4, 4 ]));

			$this->assertEquals('Root #2 : Child #2.2 : Child #2.2.2', $this->_category->titlePath(4));
			$this->assertEquals('Root #3 : Child #3.1', $this->_category->titlePath(9));
			$this->assertEquals('Root #4', $this->_category->titlePath(11));
		}
	}