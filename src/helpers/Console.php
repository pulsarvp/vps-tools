<?php
	namespace vps\tools\helpers;

	/**
	 * Class Console
	 * @package vps\tools\helpers
	 */
	class Console extends \yii\helpers\BaseConsole
	{
		/**
		 * Outputs colored string to STDOUT.
		 * ```php
		 * Console::printColor('Error', 'red');
		 * ```
		 * @param string $string
		 * @param string $color
		 */
		public static function printColor ($string, $color = 'white')
		{
			$code = 'w';
			switch ($color)
			{
				case 'black':
					$code = 'k';
					break;
				case 'cyan':
					$code = 'c';
					break;
				case 'gray':
				case 'grey':
					$code = 'K';
					break;
				case 'green':
					$code = 'g';
					break;
				case 'magenta':
					$code = 'm';
					break;
				case 'red':
					$code = 'r';
					break;
				case 'yellow':
					$code = 'y';
					break;
			}
			self::stdout(self::renderColoredString("%" . $code . $string . "%n\n"));
		}

		/**
		 * Prints given data as table to STDOUT.
		 * ```php
		 * Console::printTable('ffprobe');
		 * ```
		 * @param array $data
		 * @param array $headers
		 */
		public static function printTable ($data, $headers = [ ])
		{
			// Count max length for every column.
			$lengths = [ ];

			if (count($headers) > 0)
			{
				foreach ($headers as $i => $header)
					$lengths[ $i ] = mb_strlen($header);
			}

			foreach ($data as $row)
			{
				$row = array_values($row);
				foreach ($row as $i => $cell)
				{
					if (isset( $lengths[ $i ] ))
						$lengths[ $i ] = max($lengths[ $i ], mb_strlen($cell));
					else
						$lengths[ $i ] = mb_strlen($cell);
				}
			}

			// Print headers.
			if (count($headers) > 0)
			{
				foreach ($headers as $i => $header)
					echo str_pad($header, $lengths[ $i ] + 1, ' ', STR_PAD_RIGHT) . self::renderColoredString('%K| %n');
				echo "\n";

				self::printColor(str_repeat('-', array_sum($lengths) + count($lengths) * 3 - 1), 'grey');
			}

			// Print data.
			foreach ($data as $row)
			{
				$row = array_values($row);
				foreach ($row as $i => $cell)
				{
					echo str_pad($cell, $lengths[ $i ] + 1, ' ', STR_PAD_RIGHT) . self::renderColoredString('%K| %n');
				}
				echo "\n";
			}
		}
	}