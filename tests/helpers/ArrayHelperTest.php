<?php
	namespace tests\helpers;

	use vps\tools\helpers\ArrayHelper;
	use \yii\base\ErrorException;

	class ArrayStruct
	{
		public $a;
		public $b;
		public $children;

		public function __construct ($a, $b = null, $chilren = [ ])
		{
			$this->a = $a;
			$this->b = $b;
			$this->children = $chilren;
		}
	}

	class ArrayStruct2
	{
		public $a;
		public $b2;
		public $children2;

		public function __construct ($a, $b = null, $chilren = [ ])
		{
			$this->a = $a;
			$this->b2 = $b;
			$this->children2 = $chilren;
		}
	}

	class ArrayHelperTest extends \PHPUnit\Framework\TestCase
	{
		public function testAddColumn ()
		{
			$this->assertEquals([ [ 1, 1 ], [ 2, 2 ], [ 3, 3 ] ], ArrayHelper::addColumn([ [ 1 ], [ 2 ], [ 3 ] ], [ 1, 2, 3 ]));
			$this->assertEquals([ 'a' => [ 1, 4 ], 'c' => [ 2, 7 ], 'b' => [ 3, 6 ] ], ArrayHelper::addColumn([ 'a' => [ 1 ], 'c' => [ 2 ], 'b' => [ 3 ] ], [ 'a' => 4, 'b' => 6, 'c' => 7 ]));
			$this->assertEquals([ 'a' => [ 1 ], 'c' => [ 2 ], 'b' => [ 3 ] ], ArrayHelper::addColumn([ 'a' => [ 1 ], 'c' => [ 2 ], 'b' => [ 3 ] ], [ 'a' => 4, 'b' => 6, 'd' => 7 ]));

			$this->expectException(ErrorException::class);
			$this->expectExceptionMessage('Cannot use a scalar value as an array');
			ArrayHelper::addColumn([ 1, 2, 3 ], [ 1, 2, 3 ]);
		}

		public function testDelete ()
		{
			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			$this->assertEquals(1, ArrayHelper::delete($array, 0));
			$this->assertEquals([ 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 1 => 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			$this->assertEquals([ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], ArrayHelper::delete($array, 'a'));
			$this->assertEquals([ 0 => 1, 'key' => 0, 1 => 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			$this->assertEquals([ 'b1' => 10, 'b2' => 20 ], ArrayHelper::delete($array, 'a.b'));
			$this->assertEquals([ 0 => 1, 'a' => [ 'c' => [ 10, 6 ] ], 'key' => 0, 1 => 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			$this->assertEquals(10, ArrayHelper::delete($array, 'a.c.0'));
			$this->assertEquals([ 0 => 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 1 => 6 ] ], 'key' => 0, 1 => 10 ], $array);

			$array = null;
			$this->assertNull(ArrayHelper::delete($array, [ 12, 2312, 23 ]));
		}

		public function testEqual ()
		{
			$this->assertNull(ArrayHelper::equal('asdas'));
			$this->assertNull(ArrayHelper::equal(null));

			$this->assertFalse(ArrayHelper::equal([ 1, 2, 3 ]));
			$this->assertFalse(ArrayHelper::equal([ 0, '0' ], true));
			$this->assertTrue(ArrayHelper::equal([ 1 ]));
			$this->assertTrue(ArrayHelper::equal([ 1 ], true));
			$this->assertTrue(ArrayHelper::equal([ 0, '0' ]));
			$this->assertTrue(ArrayHelper::equal([ 1, 1, 1 ]));
			$this->assertTrue(ArrayHelper::equal([ 'a', 'a', 'a' ]));
		}

		public function testEmptyToNull ()
		{
			$this->assertNull(ArrayHelper::emptyToNull(null));
			$this->assertNull(ArrayHelper::emptyToNull('adsadas'));

			$this->assertEquals([ 1, 'a' => [ 'b' => [ 'b1' => null, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => null, 10 ], ArrayHelper::emptyToNull([ 1, 'a' => [ 'b' => [ 'b1' => 0, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => '', 10 ]));
		}

		public function testFilterKeys ()
		{
			$this->assertNull(ArrayHelper::filterKeys('asdsadsa', 'asda'));
			$this->assertEquals([ ], ArrayHelper::filterKeys([ 1, 3, 3, 3 ], 'asda'));
			$this->assertEquals([ 1 => 3 ], ArrayHelper::filterKeys([ 1, 3, 3, 3 ], 1));
			$this->assertEquals([ 1 => 3, 3 => 8 ], ArrayHelper::filterKeys([ 1, 3, 3, 8 ], [ 1, 3 ]));
			$this->assertEquals([ 'p' => 'o' ], ArrayHelper::filterKeys([ 1, 'b' => 3, 'p' => 'o', 8 ], [ 'p', 3 ]));
		}

		public function testFlatten ()
		{
			$this->assertNull(ArrayHelper::flatten(null));
			$this->assertNull(ArrayHelper::flatten('adasdasd'));

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			$this->assertEquals([ 1, 10, 20, 10, 6, 0, 10 ], ArrayHelper::flatten($array));
		}

		public function testKeysExist ()
		{
			$this->assertNull(ArrayHelper::keysExist(null, 12));
			$this->assertNull(ArrayHelper::keysExist('adasdasd', 12));

			$this->assertFalse(ArrayHelper::keysExist([ 1, 2, 3 ], 'a'));
			$this->assertTrue(ArrayHelper::keysExist([ 1, 2, 3 ], 1));
			$this->assertTrue(ArrayHelper::keysExist([ 1, 2, 3, 'b' => 9 ], [ 1, 'b', 2 ]));
		}

		public function testMergeColumns ()
		{
			$this->assertNull(ArrayHelper::mergeColumns('adsas'));
			$this->assertNull(ArrayHelper::mergeColumns(null, 'sada'));
			$this->assertNull(ArrayHelper::mergeColumns([ 1, 2, 3 ], 'ads'));
			$this->assertNull(ArrayHelper::mergeColumns([ 1, 2, 3 ], [ 1, 2 ]));

			$this->assertEquals([ ], ArrayHelper::mergeColumns([ ], [ ]));
			$this->assertEquals([ [ 1, 4 ], [ 2, 5 ], [ 3, 6 ] ], ArrayHelper::mergeColumns([ 1, 2, 3 ], [ 4, 5, 6 ]));
		}

		public function testMix ()
		{
			$this->assertNull(ArrayHelper::mix('asdasd', 1));
			$this->assertNull(ArrayHelper::mix(null, 1));
			$this->assertNull(ArrayHelper::mix(123, 1));

			$this->assertEquals([ ], ArrayHelper::mix([ ], 1));
			$this->assertEquals([ ], ArrayHelper::mix([ 1 ], 0));

			$array = [ '12', 50, [ 'sad', 'fdv', 0 ], 'ds', 32 ];

			$mix = ArrayHelper::mix($array, 2);
			$this->assertCount(2, $mix);
			foreach ($mix as $item)
				$this->assertContains($item, $array);

			$mix = ArrayHelper::mix($array, 120);
			$this->assertCount(5, $mix);
			foreach ($mix as $item)
				$this->assertContains($item, $array);
		}

		public function testObjectsAttribute ()
		{
			$this->assertNull(ArrayHelper::objectsAttribute('adsadsa', 12312));
			$this->assertNull(ArrayHelper::objectsAttribute(null, 12312));

			$this->assertEquals([ 'sadad', 10 ], ArrayHelper::objectsAttribute([
				new ArrayStruct('sadad', 12),
				new ArrayStruct(10, 'sada')
			], 'a'));

			$this->assertEquals([ 12, 'sada', null ], ArrayHelper::objectsAttribute([
				new ArrayStruct('sadad', 12),
				new ArrayStruct(10, 'sada'),
				new ArrayStruct2(1, 2)
			], 'b'));
		}

		public function testObjectsAttributeRecursive ()
		{
			$this->assertNull(ArrayHelper::objectsAttributeRecursive('adsadsa', 12312));
			$this->assertNull(ArrayHelper::objectsAttributeRecursive(null, 12312));

			$this->assertEquals([ 'sadad', 10 ], ArrayHelper::objectsAttributeRecursive([
				new ArrayStruct('sadad', 12, [ ]),
				new ArrayStruct(10, 'sada')
			], 'a'));

			$this->assertEquals([ 'sadad', 'test', 'test2', 10 ], ArrayHelper::objectsAttributeRecursive([
				new ArrayStruct('sadad', 12, [
					new ArrayStruct('test', 123),
					new ArrayStruct('test2', 123),
				]),
				new ArrayStruct(10, 'sada')
			], 'a'));

			$this->assertEquals([ 'sadad', 'test', 'test2', 'ch3', 'ch8', 10 ], ArrayHelper::objectsAttributeRecursive([
				new ArrayStruct('sadad', 12, [
					new ArrayStruct('test', 123),
					new ArrayStruct('test2', 123, [
						new ArrayStruct('ch3', 1),
						new ArrayStruct('ch8', 1),
					]),
				]),
				new ArrayStruct(10, 'sada')
			], 'a'));

			$this->assertEquals([ 'sadad', 'test', 'test2', 'ch3', 'ch8', 10 ], ArrayHelper::objectsAttributeRecursive([
				new ArrayStruct2('sadad', 12, [
					new ArrayStruct2('test', 123),
					new ArrayStruct2('test2', 123, [
						new ArrayStruct2('ch3', 1),
						new ArrayStruct2('ch8', 1),
					]),
				]),
				new ArrayStruct2(10, 'sada')
			], 'a', 'children2'));
		}

		public function testSetValue ()
		{
			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			ArrayHelper::setValue($array, 0, 5);
			$this->assertEquals([ 5, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			ArrayHelper::setValue($array, 'a.b', '-');
			$this->assertEquals([ 1, 'a' => [ 'b' => '-', 'c' => [ 10, 6 ] ], 'key' => 0, 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			ArrayHelper::setValue($array, 'a.d', 15);
			$this->assertEquals([ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ], 'd' => 15 ], 'key' => 0, 10 ], $array);

			$array = [ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6 ] ], 'key' => 0, 10 ];
			ArrayHelper::setValue($array, 'a.c.2', 15);
			$this->assertEquals([ 1, 'a' => [ 'b' => [ 'b1' => 10, 'b2' => 20 ], 'c' => [ 10, 6, 15 ] ], 'key' => 0, 10 ], $array);
		}
		public function testIsAssoc ()
		{
			$array = [ 'a' => 10, 'b' => 20, 'c' => '1' ];
			$this->assertTrue(ArrayHelper::isAssoc($array));
			
			$array = [ 10, 20, 'c' => '1' ];
			$this->assertTrue(ArrayHelper::isAssoc($array));
			
			$array = [  10, 'b', 'c'  ];
			$this->assertFalse(ArrayHelper::isAssoc($array));
			
		}
	}
