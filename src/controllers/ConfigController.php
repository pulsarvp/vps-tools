<?php

	namespace vps\tools\controllers;

	use vps\tools\helpers\Console;
	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\RemoteFileHelper;
	use Yii;

	class ConfigController extends \yii\console\Controller
	{

		public function actionInfo ($name = null)
		{

			if (is_null($name))
				$name = [ 'api', 'frontend', 'backend', 'console' ];
			else
				$name = [ $name ];

			foreach ($name as $item)
			{
				if ($item != 'console')
					$this->getInfo($item);
				else
					$this->getInfoConsole();
			}
		}

		public function actionController ($name = null)
		{
			if (is_null($name))
				$name = [ 'api', 'frontend', 'backend', 'console' ];
			else
				$name = [ $name ];

			foreach ($name as $item)
			{

				if ($item != 'api')
					$this->getControllersAndActions($item);
				else
					$this->getControllersAndActionsApi($item);
			}
		}

		private function getControllersAndActions ($name)
		{
			if (RemoteFileHelper::exists(BASE_PATH . '/../' . $name))
			{
				Console::printColor($name, 'cyan');
				$controllerlist = FileHelper::listPatternItems(BASE_PATH . '/../' . $name . '/controllers', '*', true);

				$this->printActions($controllerlist);
			}
		}

		private function getControllersAndActionsApi ($name)
		{
			if (RemoteFileHelper::exists(BASE_PATH . '/../' . $name))
			{
				Console::printColor($name, 'cyan');
				$versionList = FileHelper::listPatternItems(BASE_PATH . '/../' . $name . '/modules', 'v?', true);
				foreach ($versionList as $version)
				{
					$versionName = strtolower(substr($version, strrpos($version, DIRECTORY_SEPARATOR) + 1, 2));
					Console::printColor($versionName, 'magenta');
					$controllerlist = FileHelper::listPatternItems($version . '/controllers/', '*', true);
					$this->printActions($controllerlist);
				}
			}
		}

		private function printActions ($controllerlist)
		{
			asort($controllerlist);
			foreach ($controllerlist as $controller)
			{
				$controllerName = strtolower(substr($controller, strrpos($controller, DIRECTORY_SEPARATOR) + 1, -14));

				Console::printColor('- ' . $controllerName, 'yellow');

				$handle = fopen($controller, "r");
				if ($handle)
				{
					$contents = fread($handle, filesize($controller));
					if (preg_match('/public function actions\s?\(\)(.*?)return\s(.*?)\;/s', $contents, $display))
					{
						preg_match_all('/\'(\w*)\'\s*\=\>\s\[(.*?)\]/s', $display[ 2 ], $matches, PREG_SET_ORDER, 0);

						foreach ($matches as $key => $item)
						{
							if (isset($item[ 1 ]) and $item[ 1 ] != 'with')
								Console::printColor("   $controllerName/" . $item[ 1 ], 'green');
						}
					}

					rewind($handle);
					while (( $line = fgets($handle) ) !== false)
					{
						if (preg_match('/public function action(.*?)\(/', $line, $display))
						{
							if (strlen($display[ 1 ]) > 2)
							{
								Console::printColor("   $controllerName/" . strtolower($display[ 1 ]), 'green');
							}
						}
					}
				}
				fclose($handle);
				Console::printColor('');
			}
		}

		private function getInfo ($name)
		{

			if (RemoteFileHelper::exists(BASE_PATH . '/../' . $name))
			{
				Console::printColor($name, 'cyan');
				$handle = fopen(BASE_PATH . '/../' . $name . '/bootstrap.php', "r");
				if ($handle)
				{
					while (( $line = fgets($handle) ) !== false)
					{
						if (preg_match('/\$config = require BASE_PATH \. (.*?)$/', $line, $display))
						{
							if (preg_match('/YII_DEBUG\s?\?\s?\'(.*?)\'/', $display[ 1 ], $file))
								$file = '/config/' . trim($file[ 1 ], '\';') . '.php';
							else
								$file = trim($display[ 1 ], '\';');
							$config = require BASE_PATH . '/../' . $name . $file;

							foreach ([ 'version', 'id', 'language' ] as $name)
							{
								if (isset($config[ $name ]))
									Console::printColor('   ' . $name . ' = ' . $config[ $name ], 'green');
							}
							Console::printColor('components:', 'yellow');
							foreach ($config[ 'components' ] as $key => $item)
							{
								Console::printColor('   ' . $key, 'green');
							}
						}
					}
				}
				fclose($handle);
				Console::printColor('');
			}
		}

		private function getInfoConsole ()
		{
			Console::printColor('console', 'cyan');

			foreach ([ 'version', 'id', 'language' ] as $name)
			{
				if (isset(Yii::$app->$name))
					Console::printColor('   ' . $name . ' = ' . Yii::$app->$name, 'green');
			}
			Console::printColor('components:', 'yellow');
			foreach (Yii::$app->getComponents() as $key => $item)
			{
				Console::printColor('   ' . $key, 'green');
			}
			Console::printColor('');
		}

	}