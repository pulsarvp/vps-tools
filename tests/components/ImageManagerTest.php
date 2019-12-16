<?php

namespace components;

use vps\tools\components\ImageManager;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase
{
	public function testSaveImageWithResizingSuccessful ()
	{
		$this->createImage();
		$result = (new ImageManager())->saveImage($this->tempDir(), $this->uploadedFile(), true);

		$this->assertArrayHasKey('original', $result);
		$this->assertArrayHasKey('sd', $result);
		$this->assertArrayHasKey('hd', $result);

		$realFileName = $this->realFileNameWithExtension($result[ImageManager::F_ORIGINAL]);

		$this->assertEquals(
			implode(
				'',
				[
					$this->tempDir(),
					DIRECTORY_SEPARATOR,
					$realFileName[0],
					DIRECTORY_SEPARATOR,
					$realFileName[1],
					DIRECTORY_SEPARATOR,
					$realFileName
				]
			),
			$result[ImageManager::F_ORIGINAL]
		);

		$this->assertEquals(
			implode(
				'',
				[
					$this->tempDir(),
					DIRECTORY_SEPARATOR,
					ImageManager::F_HD,
					DIRECTORY_SEPARATOR,
					$realFileName[0],
					DIRECTORY_SEPARATOR,
					$realFileName[1],
					DIRECTORY_SEPARATOR,
					$realFileName
				]
			),
			$result[ImageManager::F_HD]
		);

		$this->assertEquals(
			implode(
				'',
				[
					$this->tempDir(),
					DIRECTORY_SEPARATOR,
					ImageManager::F_SD,
					DIRECTORY_SEPARATOR,
					$realFileName[0],
					DIRECTORY_SEPARATOR,
					$realFileName[1],
					DIRECTORY_SEPARATOR,
					$realFileName
				]
			),
			$result[ImageManager::F_SD]
		);

		$this->assertTrue(file_exists($result[ImageManager::F_ORIGINAL]));
		$this->assertTrue(file_exists($result[ImageManager::F_SD]));
		$this->assertTrue(file_exists($result[ImageManager::F_HD]));
	}

	private function uploadedFile ()
	{
		$mock = new \stdClass();
		$mock->extension = $this->extension();
		$mock->tempName = $this->filenameWithPath();
		return $mock;
	}

	private function createImage ()
	{
		$img = imagecreatetruecolor(120, 20);
		imagejpeg($img,$this->filenameWithPath(),100);
	}

	private function tempDir (): string
	{
		return sys_get_temp_dir();
	}

	private function filenameWithPath (): string
	{
		return $this->tempDir() . DIRECTORY_SEPARATOR . 'test_file_name_for_save_image_test.' . $this->extension();
	}

	private function extension (): string
	{
		return 'jpg';
	}

	private function realFileNameWithExtension (string $filenameWithPath): string
	{
		preg_match("/[a-z0-9\-]{36}\.{$this->extension()}$/", $filenameWithPath, $matches);
		$this->assertCount(1, $matches);
		return $matches[0];
	}
}
