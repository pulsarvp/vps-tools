<?php
	namespace tests\helpers;

	use vps\tools\helpers\FileHelper;

	class FileHelperTest extends \PHPUnit\Framework\TestCase
	{
		public $datapath = __DIR__ . '/../data/file_helper';

		public static function init ()
		{
			/**
			 * + dir_1
			 *   + dir_1_1
			 *     - file1.txt
			 *     - file2.txt
			 *   + dir_1_2
			 *     + dir_1_2_1
			 *       - file5.txt
			 *     + dir_1_2_2
			 *     - file6.txt
			 *     - file7.txt
			 *   + dir_1_3
			 *     - file8.txt
			 *     - file9.txt
			 * + dir_2
			 * + dir_3
			 *   - file10.txt
			 * - file3.txt
			 * - file4.txt
			 * + zdir
			 */

			$datapath = ( new self )->datapath;

			if (is_dir($datapath))
				shell_exec('rm -rf ' . escapeshellarg($datapath) . '/*');

			if (is_dir($datapath) or mkdir($datapath, 0755, true))
			{
				mkdir($datapath . '/dir_1/dir_1_1', 0755, true);
				mkdir($datapath . '/dir_1/dir_1_2/dir_1_2_2', 0755, true);
				sleep(1);
				mkdir($datapath . '/dir_1/dir_1_2/dir_1_2_1', 0755, true);
				mkdir($datapath . '/dir_1/dir_1_3', 0755, true);
				mkdir($datapath . '/dir_2', 0755, true);
				mkdir($datapath . '/dir_3', 0700, true);
				mkdir($datapath . '/zdir', 0755, true);

				file_put_contents($datapath . '/dir_1/dir_1_1/file1.txt', 'File #1');
				file_put_contents($datapath . '/dir_1/dir_1_1/file2.txt', 'File #2');
				file_put_contents($datapath . '/file3.txt', 'File #3');
				file_put_contents($datapath . '/file4.txt', 'File #4');
				file_put_contents($datapath . '/dir_1/dir_1_2/dir_1_2_1/file5.txt', 'File #5');
				sleep(1);
				file_put_contents($datapath . '/dir_1/dir_1_2/file7.txt', 'File #7');
				sleep(1);
				file_put_contents($datapath . '/dir_1/dir_1_2/file6.txt', 'File #6');
				file_put_contents($datapath . '/dir_1/dir_1_3/file8.txt', 'File #8');
				file_put_contents($datapath . '/dir_1/dir_1_3/file9.txt', 'File #9');
				file_put_contents($datapath . '/dir_3/file10.txt', 'File #10');
			}
			else
				exit( 'Error when creating data directory.' );
		}

		public function testClearDir ()
		{
			self::init();

			$this->assertFalse(FileHelper::clearDir('adsds'));
			$this->assertFalse(FileHelper::clearDir(null));

			$this->assertTrue(FileHelper::clearDir($this->datapath . '/dir_1/dir_1_3'));
			$this->assertTrue(is_dir($this->datapath . '/dir_1/dir_1_3'));
			$this->assertEquals(0, FileHelper::countItemsInDir($this->datapath . '/dir_1/dir_1_3'));

			$this->assertTrue(FileHelper::clearDir($this->datapath . '/dir_1'));
			$this->assertTrue(is_dir($this->datapath . '/dir_1'));
			$this->assertEquals(0, FileHelper::countItemsInDir($this->datapath . '/dir_1'));

			$this->assertTrue(FileHelper::clearDir($this->datapath));
			$this->assertTrue(is_dir($this->datapath));
			$this->assertEquals(0, FileHelper::countItemsInDir($this->datapath));

			self::init();
		}

		public function testCountItems ()
		{
			$this->assertNull(FileHelper::countItemsInDir('adsdaad'));
			$this->assertNull(FileHelper::countItemsInDir(null));

			$this->assertEquals(12, FileHelper::countItems($this->datapath . '/dir_1'));
			$this->assertEquals(5, FileHelper::countItems($this->datapath . '/dir_1/dir_1_2'));
		}

		public function testCountItemsInDir ()
		{
			$this->assertNull(FileHelper::countItemsInDir('adsdaad'));

			$this->assertEquals(3, FileHelper::countItemsInDir($this->datapath . '/dir_1'));
			$this->assertEquals(4, FileHelper::countItemsInDir($this->datapath . '/dir_1/dir_1_2'));
		}

		public function testDeleteFile ()
		{
			$this->assertFalse(FileHelper::deleteFile('adskjcnzxk'));
			$this->assertFalse(FileHelper::deleteFile($this->datapath));

			$this->assertTrue(FileHelper::deleteFile($this->datapath . '/dir_1/dir_1_1/file1.txt'));
			$this->assertFalse(file_exists($this->datapath . '/dir_1/dir_1_1/file1.txt'));

			self::init();
		}

		public function testListDirs ()
		{
			$this->assertNull(FileHelper::listItems('ashdjghajsdgj'));

			$this->assertEquals([ 'dir_1', 'dir_2', 'dir_3', 'zdir' ], FileHelper::listDirs($this->datapath));
			$this->assertEquals([
				realpath($this->datapath . '/dir_1'),
				realpath($this->datapath . '/dir_2'),
				realpath($this->datapath . '/dir_3'),
				realpath($this->datapath . '/zdir')
			], FileHelper::listDirs($this->datapath, true));

			$this->assertEquals([], FileHelper::listDirs($this->datapath . '/dir_3'));

			$this->assertEquals([
				realpath($this->datapath . '/dir_1/dir_1_2/dir_1_2_1'),
				realpath($this->datapath . '/dir_1/dir_1_2/dir_1_2_2')
			], FileHelper::listDirs($this->datapath . '/dir_1/dir_1_2', true));
		}

		public function testListFiles ()
		{
			$this->assertNull(FileHelper::listFiles('ashdjghajsdgj'));

			$this->assertEquals([ 'file3.txt', 'file4.txt' ], FileHelper::listFiles($this->datapath));
			$this->assertEquals([
				realpath($this->datapath . '/file3.txt'),
				realpath($this->datapath . '/file4.txt')
			], FileHelper::listFiles($this->datapath, true));

			$this->assertEquals([ 'file10.txt' ], FileHelper::listFiles($this->datapath . '/dir_3'));

			$this->assertEquals([
				realpath($this->datapath . '/dir_1/dir_1_2/file6.txt'),
				realpath($this->datapath . '/dir_1/dir_1_2/file7.txt')
			], FileHelper::listFiles($this->datapath . '/dir_1/dir_1_2', true));
		}

		public function testListItems ()
		{
			$this->assertNull(FileHelper::listItems('ashdjghajsdgj'));

			$this->assertEquals([ 'dir_1', 'dir_2', 'dir_3', 'file3.txt', 'file4.txt', 'zdir' ], FileHelper::listItems($this->datapath));
			$this->assertEquals([
				realpath($this->datapath . '/dir_1'),
				realpath($this->datapath . '/dir_2'),
				realpath($this->datapath . '/dir_3'),
				realpath($this->datapath . '/file3.txt'),
				realpath($this->datapath . '/file4.txt'),
				realpath($this->datapath . '/zdir')
			], FileHelper::listItems($this->datapath, true));

			$this->assertEquals([ 'file10.txt' ], FileHelper::listItems($this->datapath . '/dir_3'));

			$this->assertEquals([
				realpath($this->datapath . '/dir_1/dir_1_2/dir_1_2_1'),
				realpath($this->datapath . '/dir_1/dir_1_2/dir_1_2_2'),
				realpath($this->datapath . '/dir_1/dir_1_2/file6.txt'),
				realpath($this->datapath . '/dir_1/dir_1_2/file7.txt')
			], FileHelper::listItems($this->datapath . '/dir_1/dir_1_2', true));
		}

		public function testMimetypeFile ()
		{
			self::init();

			$this->assertEquals(FileHelper::MIME_DIR, FileHelper::mimetypeFile($this->datapath));
			$this->assertEquals(FileHelper::MIME_TXT, FileHelper::mimetypeFile($this->datapath . '/dir_1/dir_1_1/file1.txt'));
			$this->assertEquals(FileHelper::MIME_XML, FileHelper::mimetypeFile($this->datapath . '/../../phpunit.xml'));
			$this->assertEquals(FileHelper::MIME_PHP, FileHelper::mimetypeFile($this->datapath . '/../../bootstrap.php'));
		}

		public function testListItemsByDate ()
		{
			$this->assertNull(FileHelper::listItemsByDate('ashdjghajsdgj'));
			$this->assertNull(FileHelper::listItemsByDate(null));

			$this->assertEquals([ 'dir_1_2_2', 'dir_1_2_1', 'file7.txt', 'file6.txt' ], FileHelper::listItemsByDate($this->datapath . '/dir_1/dir_1_2', SORT_ASC));
			$this->assertEquals([ 'file6.txt', 'file7.txt', 'dir_1_2_1', 'dir_1_2_2' ], FileHelper::listItemsByDate($this->datapath . '/dir_1/dir_1_2'));
		}

		public function testListPatternsItems ()
		{
			$this->assertNull(FileHelper::listPatternItems('ashdjghajsdgj'));

			$this->assertEquals(FileHelper::listItems($this->datapath), FileHelper::listPatternItems($this->datapath));
			$this->assertEquals([ 'dir_1_2_1', 'dir_1_2_2' ], FileHelper::listPatternItems($this->datapath . '/dir_1/dir_1_2', 'dir_*'));
			$this->assertEquals([ 'file6.txt', 'file7.txt' ], FileHelper::listPatternItems($this->datapath . '/dir_1/dir_1_2', '*.txt'));
			$this->assertEquals([
				$this->datapath . '/dir_1/dir_1_2/file6.txt',
				$this->datapath . '/dir_1/dir_1_2/file7.txt'
			], FileHelper::listPatternItems($this->datapath . '/dir_1/dir_1_2', '*.txt', true));
		}

		public function testListRelativeFiles ()
		{
			$this->assertNull(FileHelper::listRelativeFiles('ashdjghajsdgj', 'sadasd'));

			$this->assertEquals([
				'dir_1_2/dir_1_2_1/file5.txt',
				'dir_1_2/file6.txt',
				'dir_1_2/file7.txt'
			], FileHelper::listRelativeFiles($this->datapath . '/dir_1/dir_1_2', $this->datapath . '/dir_1'));
		}

		public function testExtension ()
		{
			self::init();

			$this->assertEquals('', FileHelper::extension($this->datapath));
			$this->assertEquals('php', FileHelper::extension($this->datapath . '/dir_1/dir_1_1/file11.php'));
			$this->assertEquals('txt', FileHelper::extension($this->datapath . '/dir_1/dir_1_1/file1.txt'));
			$this->assertEquals('xml', FileHelper::extension($this->datapath . '/../../phpunit.xml'));
			$this->assertEquals('php', FileHelper::extension($this->datapath . '/../../bootstrap.php'));
		}
	}