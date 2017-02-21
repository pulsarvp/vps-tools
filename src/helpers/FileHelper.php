<?php
	namespace vps\tools\helpers;

	/**
	 * Class FileHelper
	 * @package vps\tools\helpers
	 */
	class FileHelper extends \yii\helpers\BaseFileHelper
	{
		const MIME_DIR = 'directory';
		const MIME_PHP = 'text/x-php';
		const MIME_TXT = 'text/plain';
		const MIME_XML = 'application/xml';

		/**
		 * Clears given directory without deleting it itself.
		 * ```php
		 * FileHelper::clearDir('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param  string $path
		 * @return boolean
		 */
		public static function clearDir ($path)
		{
			if (is_dir($path) and is_writable($path) and ( $dir = opendir($path) ) !== false)
			{
				while ($f = readdir($dir))
				{
					if ($f != '.' and $f != '..')
					{
						if (is_file($path . '/' . $f) and is_writable($path . '/' . $f))
							unlink($path . '/' . $f);
						else
						{
							self::clearDir($path . '/' . $f);
							@rmdir($path . '/' . $f);
						}
					}
				}
				closedir($dir);

				return true;
			}

			return false;
		}

		/**
		 * Recursively count files and directories in given directory.
		 * ```php
		 * $result = FileHelper::countItems('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param string $path
		 * @return int|null
		 */
		public static function countItems ($path)
		{
			if (is_dir($path) and ( $dir = opendir($path) ) !== false)
			{
				$return = self::countItemsInDir($path);
				while ($f = readdir($dir))
				{
					if ($f != '.' and $f != '..' and is_dir($path . '/' . $f))
						$return += self::countItems($path . '/' . $f);
				}
				closedir($dir);

				return $return;
			}

			return null;
		}

		/**
		 * Counts files and directories in given directory. Not recursive.
		 * ```php
		 * $result = FileHelper::countItemsInDir('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param  string $path The directory under which the items should be counted.
		 * @return integer|null
		 */
		public static function countItemsInDir ($path)
		{
			if (is_dir($path))
			{
				$it = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);

				return iterator_count($it);
			}
			else
				return null;
		}

		/**
		 * Deletes given file without rising an exception.
		 * ```php
		 * FileHelper::deleteFile('/var/www/dir_1/dir_1_3/file.txt');
		 * ```
		 *
		 * @param string $path
		 * @return bool
		 */
		public static function deleteFile ($path)
		{
			if (file_exists($path))
			{
				if (is_writable($path))
					return @unlink($path);
				else
					return false;
			}

			return false;
		}

		/**
		 * Gets directories list in given directory.
		 * ```php
		 * $result = FileHelper::listDirs('/var/www/dir_1');
		 * ```
		 *
		 * @param  string  $path     The directory under which the items will be looked for.
		 * @param  boolean $absolute Whether return path to items should be absolute.
		 * @return array|null List of paths to the found items.
		 */
		public static function listDirs ($path, $absolute = false)
		{
			if (is_dir($path) and is_readable($path))
			{
				$data = [];
				$it = new \FilesystemIterator($path);
				foreach ($it as $item)
				{
					if ($item->isDir())
						$data[] = $absolute ? $item->getRealPath() : $item->getFilename();
				}

				return $data;
			}

			return null;
		}

		/**
		 * Gets files list in given directory.
		 * ```php
		 * $result = FileHelper::listFiles('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param  string  $path     The directory under which the items will be looked for.
		 * @param  boolean $absolute Whether return path to items should be absolute.
		 * @return array|null List of paths to the found items.
		 */
		public static function listFiles ($path, $absolute = false)
		{
			if (is_dir($path) and is_readable($path))
			{
				$data = [];
				$it = new \FilesystemIterator($path);
				foreach ($it as $item)
				{
					if ($item->isFile())
						$data[] = $absolute ? $item->getRealPath() : $item->getFilename();
				}

				return $data;
			}

			return null;
		}

		/**
		 * Gets files and directories list in given directory.
		 * ```php
		 * $result = FileHelper::listItems('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param  string  $path     The directory under which the items will be looked for.
		 * @param  boolean $absolute Whether return path to items should be absolute.
		 * @return array|null List of paths to the found items.
		 */
		public static function listItems ($path, $absolute = false)
		{
			if (is_dir($path) and is_readable($path))
			{
				$data = [];

				$it = new \FilesystemIterator($path);
				foreach ($it as $item)
					$data[] = $absolute ? $item->getRealPath() : $item->getFilename();

				return $data;
			}

			return null;
		}

		/**
		 * Gets files and directories list in given directory and order it by
		 * modification time. Not recursive.
		 * ```php
		 * $result = FileHelper::listItemsByDate('/var/www/dir_1/dir_1_3');
		 * ```
		 *
		 * @param  string  $path  The directory under which the files will be looked for.
		 * @param  integer $order Order direction. Default is descending.
		 * @return array|null Array of pairs 'modification time - full path to the file'.
		 */
		public static function listItemsByDate ($path, $order = SORT_DESC)
		{
			if (is_dir($path) and is_readable($path))
			{
				$data = [];
				$time = [];

				$it = new \FilesystemIterator($path);
				foreach ($it as $item)
				{
					$data[] = $item->getFilename();
					$time[] = $item->getMTime();
				}

				array_multisort($time, $order, $data);

				return $data;
			}

			return null;
		}

		/**
		 * Gets files list in given directory that match pattern.
		 * ```php
		 * $result = FileHelper::listPatternItems('/var/www/dir_1', '*.php');
		 * ```
		 *
		 * @param  string  $pattern
		 * @param  string  $path     The directory under which the items will be looked for.
		 * @param  boolean $absolute Whether return path to items should be absolute.
		 * @return array List of paths to the found items.
		 */
		public static function listPatternItems ($path, $pattern = '*', $absolute = false)
		{
			if (is_dir($path) and is_readable($path))
			{
				$files = glob($path . '/' . $pattern);

				if ($absolute)
					return $files;

				$data = [];
				$n = strlen($path . '/');

				foreach ($files as $file)
					$data[] = substr($file, $n);

				return $data;
			}

			return null;
		}

		/**
		 * Finds recursively files in given path and return list of paths relative to second parameter.
		 * ```php
		 * $result = FileHelper::listRelativeFiles('/var/www/dir_1/dir_1_3',/var/www/dir_1);
		 * ```
		 *
		 * @param  string $path
		 * @param  string $relativepath
		 * @return array
		 */
		public static function listRelativeFiles ($path, $relativepath)
		{
			if (is_dir($path) and is_readable($path))
			{
				$data = [];
				$list = self::findFiles($path);
				$relativepath = rtrim($relativepath, '/') . '/';
				$n = strlen($relativepath);
				foreach ($list as $item)
				{
					if (strpos($item, $relativepath) === 0)
						$data[] = substr_replace($item, '', 0, $n);
				}

				return $data;
			}

			return null;
		}

		/**
		 * Get mimetype of the given file.
		 * ```php
		 * $result = FileHelper::mimetypeFile('/var/www/phpunit.xml');
		 * ```
		 *
		 * @param  string $path Path to the file.
		 * @return string|null
		 */
		public static function mimetypeFile ($path)
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if ($finfo)
			{
				$info = finfo_file($finfo, $path);
				finfo_close($finfo);

				return $info;
			}

			return null;
		}

	}
