<?php
	namespace tests\helpers;

	use vps\tools\helpers\RemoteFileHelper;
	use \yii\base\ErrorException;

	class RemoteFileHelperTest extends \PHPUnit\Framework\TestCase
	{
		private $_datapath = __DIR__ . '/../data/remote_file_helper';

		public function setUp ()
		{
			shell_exec('rm -rf ' . escapeshellarg($this->_datapath));
			mkdir($this->_datapath);
			file_put_contents($this->_datapath . '/file.txt', 'File WHOAH');
		}

		public function testIsLocal ()
		{
			$this->assertFalse(RemoteFileHelper::isLocal(null));
			$this->assertFalse(RemoteFileHelper::isLocal('https://google.com'));
			$this->assertTrue(RemoteFileHelper::isLocal($this->_datapath . '/file.txt'));
		}

		public function testExists ()
		{
			$this->assertFalse(RemoteFileHelper::exists(null));
			$this->assertFalse(RemoteFileHelper::exists('dasdasdas'));
			$this->assertTrue(RemoteFileHelper::exists($this->_datapath . '/file.txt'));
			$this->assertTrue(RemoteFileHelper::exists('https://google.com'));
		}

		public function testSave ()
		{
			$this->assertTrue(RemoteFileHelper::save('https://google.com', $this->_datapath . '/google.html'));

			$this->expectException(ErrorException::class);
			$this->expectExceptionMessage('File path does not exist.');

			$this->assertFalse(RemoteFileHelper::save(null, 'null'));
			$this->assertFalse(RemoteFileHelper::save('adasdasd', 'adasdasd_saved'));
		}
	}
