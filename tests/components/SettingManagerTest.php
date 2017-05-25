<?php
	namespace tests\components;

	use Yii;

	class SettingManagerTest extends \PHPUnit\Framework\TestCase
	{
		private $_setting;
		private $_modelClass = 'tests\models\Setting';

		public function setUp ()
		{
			parent::setUp();
			$this->_setting = Yii::$app->settings;
		}

		public function testGet ()
		{
			$this->assertNull($this->_setting->get(0));
			$this->assertNull($this->_setting->get([ 'wqeq', 1, -10 ]));
			$this->assertNull($this->_setting->get('randomname'));

			$this->assertEquals(10, $this->_setting->get(0, 10));

			$this->assertEquals('?…¬∆ˆ†∑∫˜∑∑ø∑˜˚˙∂˜', $this->_setting->get('special#chars'));
			$this->assertEquals('Sun is shining in the sky', $this->_setting->get('0'));
			$this->assertEquals('de9f2c7fd25e1b3afad3e85a0bd17d9b100db4b3', $this->_setting->get('name with spaces'));
		}

		public function testAll ()
		{
			$all = $this->_setting->all;

			$this->assertInternalType('array', $all);
			$this->assertCount(7, $all);
			$this->assertContainsOnlyInstancesOf($this->_modelClass, $all);
		}
	}

