<?php
	namespace tests\framework;

	use Yii;

	class YiiTest extends \PHPUnit\Framework\TestCase
	{
		public function testP ()
		{
			$this->expectOutputString('<pre style="line-height:100%; font-size:12px">asdasd</pre><pre style="line-height:100%; font-size:12px">' . "stdClass Object\n(\n    [a] => 12\n    [0] => 21\n    [c] => s\n)\n" . '</pre>');
			Yii::p('asdasd');
			Yii::p(json_decode(json_encode([ 'a' => 12, 21, 'c' => 's' ])));
		}

		public function testTr ()
		{
			$this->assertEquals('', Yii::tr(null));
			$this->assertEquals('adsdas', Yii::tr('adsdas'));
			$this->assertEquals('текст для ссылки', Yii::tr('link text'));
		}
	}