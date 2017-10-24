<?php

	namespace vps\tools\console;

	/**
	 * @author    Anna Manaenkova <witzawitz@gmail.com>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-20
	 */
	class Application extends \yii\console\Application
	{
		protected $_nullComponents = [ 'migrate' => [ 'settings', 'cache' ] ];

		public function __construct ($config = [])
		{
			$config = $this->filterConfig($config);
			parent::__construct($config);
		}

		private function filterConfig ($config)
		{
			$setNullComponents = false;

			// Remove unnecessary components for migrations.
			$argv = $_SERVER[ 'argv' ];
			$argc = $_SERVER[ 'argc' ];

			if ($argc > 1)
			{
				list($controller,) = explode("/", $argv[ 1 ]);
				if ($controller)
				{
					if (isset($this->_nullComponents[ $controller ]))
					{
						foreach ($this->_nullComponents[ $controller ] as $component)
						{
							if (isset($config[ 'components' ][ $component ]))
								$config[ 'components' ][ $component ] = null;
						}
					}
				}
			}

			return $config;
		}
	}