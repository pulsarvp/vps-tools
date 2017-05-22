<?php
	namespace tests\helpers;

	use vps\tools\helpers\StringHelper;

	class StringHelperTest extends \PHPUnit\Framework\TestCase
	{
		public function testClear ()
		{
			$this->assertEquals('123', StringHelper::clear("123*(&(*"));
			$this->assertEquals('', StringHelper::clear("*(&(*"));
			$this->assertEquals('test   asd', StringHelper::clear("{}test  (* asd"));
		}

		public function testExplode ()
		{
			$this->assertEquals([ 'It', 'is', 'a first', 'test' ], StringHelper::explode("It, is, a first, test"));
			$this->assertEquals([ 'It', 'is', 'a test with trimmed digits', '0', '1', '2' ], StringHelper::explode("It, is, a test with trimmed digits, 0, 1, 2", ',', true, true));
			$this->assertEquals([ 'It', 'is', 'a second', 'test' ], StringHelper::explode("It+ is+ a second+ test", '+'));
			$this->assertEquals([ 'Save', '', '', 'empty trimmed string' ], StringHelper::explode("Save, ,, empty trimmed string", ',', true, false));
			$this->assertEquals([ 'Здесь', 'multibyte', 'строка' ], StringHelper::explode("Здесь我 multibyte我 строка", '我'));
			$this->assertEquals([ 'Disable', '  trim  ', 'here but ignore empty' ], StringHelper::explode("Disable,  trim  ,,,here but ignore empty", ',', false, true));
			$this->assertEquals([ 'It/', ' is?', ' a', ' test with rtrim' ], StringHelper::explode("It/, is?, a , test with rtrim", ',', 'rtrim'));
			$this->assertEquals([ 'It', ' is', ' a ', ' test with closure' ], StringHelper::explode("It/, is?, a , test with closure", ',', function ($value) { return trim($value, '/?'); }));
		}

		public function testMexplode ()
		{
			$this->assertNull(StringHelper::mexplode('dasda', true));
			$this->assertNull(StringHelper::mexplode('dasda', null));

			$this->assertEquals([ 'sd', 'dsda', 'adsad adsad', 'cs' ], StringHelper::mexplode('sd:dsda:adsad adsad;cs', [ ':', ';' ]));
			$this->assertEquals([ 'sd', 'dsda', 'adsad', 'adsad', 'cs' ], StringHelper::mexplode('sd:dsda:adsad adsad;cs', [ ':', ';', ' ' ]));
			$this->assertEquals([ 'sd', 'dsd', 'ds', 'd', 'ds', 'd', 'cs' ], StringHelper::mexplode('sd:dsda:adsad adsad;cs', [ ':', ';', ' ', 'a' ]));
			$this->assertEquals([ 'sd', 'ds', 'd', 'ds', 'd', 'ds', 'd', 'cs' ], StringHelper::mexplode('sd:ds-da:adsad adsad;cs', [ ':', ';', ' ', 'a', '-' ]));
			$this->assertEquals([ 'sd', 'ds', 'd', 'ds', 'd', 'ds', 'd', 'cs' ], StringHelper::mexplode('sd:ds*da:adsad adsad;cs', [ ':', ';', ' ', 'a', '*' ]));
		}

		public function testPos ()
		{
			$this->assertNull(StringHelper::pos(null, 'sad'));
			$this->assertNull(StringHelper::pos('a', 'sad', 10));

			$this->assertEquals(1, StringHelper::pos('lakanahbaha', 'a'));
			$this->assertEquals(10, StringHelper::pos('lakanahbahakjlapaosa', 'a', 5));
			$this->assertEquals(19, StringHelper::pos('lakanahbahakjlapaosa', 'a', -1));
			$this->assertEquals(16, StringHelper::pos('lakanahbahakjlapaosa', 'a', -2));
		}

		public function testRandom ()
		{
			$this->assertNull(StringHelper::random(-1));
			$this->assertNull(StringHelper::random(null));

			$this->assertRegExp('/[0-9a-z]{10}/', StringHelper::random());
			$this->assertRegExp('/[0-9a-z]{15}/', StringHelper::random(15));
			$this->assertRegExp('/[0-9a-zA-Z]{19}/', StringHelper::random(19, true));
		}

		public function testRpos ()
		{
			$this->assertNull(StringHelper::rpos(null, 'sad'));
			$this->assertNull(StringHelper::rpos('a', 'sad', 10));

			$this->assertEquals(1, StringHelper::rpos('lakanahbaha', 'a', -1));
			$this->assertEquals(10, StringHelper::rpos('lakanahbahakjlapaosa', 'a', -5));
			$this->assertEquals(19, StringHelper::rpos('lakanahbahakjlapaosa', 'a', 1));
			$this->assertEquals(16, StringHelper::rpos('lakanahbahakjlapaosa', 'a', 2));
		}
	}